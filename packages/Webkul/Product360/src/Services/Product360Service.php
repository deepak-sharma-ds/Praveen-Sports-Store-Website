<?php

namespace Webkul\Product360\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Product360\Repositories\Product360ImageRepository;

/**
 * Product360Service
 * 
 * Business logic layer for 360 image management.
 * Coordinates upload workflow, reordering, deletion, and data retrieval.
 */
class Product360Service
{
    /**
     * Create a new service instance.
     *
     * @param  \Webkul\Product360\Repositories\Product360ImageRepository  $imageRepository
     * @param  \Webkul\Product360\Services\Product360ImageService  $imageService
     */
    public function __construct(
        protected Product360ImageRepository $imageRepository,
        protected Product360ImageService $imageService
    ) {}

    /**
     * Process uploaded images.
     *
     * @param  array  $files
     * @param  int  $productId
     * @return array
     */
    public function uploadImages(array $files, int $productId): array
    {
        $storedFiles = [];
        
        try {
            DB::beginTransaction();
            
            // Get current max position for this product
            $maxPosition = $this->imageRepository
                ->getByProduct($productId)
                ->max('position') ?? 0;
            
            $imagesData = [];
            $position = $maxPosition + 1;
            
            // Store each file and prepare data for database
            foreach ($files as $file) {
                try {
                    $path = $this->imageService->store($file, $productId);
                    $storedFiles[] = $path;
                    
                    $imagesData[] = [
                        'path' => $path,
                        'position' => $position,
                    ];
                    
                    $position++;
                } catch (\Exception $e) {
                    // Storage error occurred - rollback and cleanup
                    throw $e;
                }
            }
            
            // Create database records
            $createdImages = $this->imageRepository->createMany($imagesData, $productId);
            
            DB::commit();
            
            // Invalidate cache
            $this->invalidateCache($productId);
            
            // Return formatted data
            return [
                'success' => true,
                'images' => $createdImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->url,
                        'position' => $image->position,
                    ];
                })->toArray(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Cleanup any files that were stored before the error
            // Continue cleanup even if individual deletions fail
            $cleanupFailures = 0;
            foreach ($storedFiles as $path) {
                try {
                    $deleted = $this->imageService->delete($path);
                    if (!$deleted) {
                        $cleanupFailures++;
                    }
                } catch (\Exception $cleanupException) {
                    $cleanupFailures++;
                    \Log::warning('Failed to cleanup file after upload error', [
                        'path' => $path,
                        'error' => $cleanupException->getMessage(),
                    ]);
                }
            }
            
            if ($cleanupFailures > 0) {
                \Log::warning('Some files could not be cleaned up after upload failure', [
                    'product_id' => $productId,
                    'failed_count' => $cleanupFailures,
                    'total_count' => count($storedFiles),
                ]);
            }
            
            \Log::error('Failed to upload 360 images', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to upload images. Please try again.',
            ];
        }
    }

    /**
     * Reorder existing images.
     *
     * @param  array  $order
     * @param  int  $productId
     * @return bool
     */
    public function reorderImages(array $order, int $productId): bool
    {
        try {
            $success = $this->imageRepository->updatePositions($order);
            
            if ($success) {
                $this->invalidateCache($productId);
            }
            
            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to reorder 360 images', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
            ]);
            
            return false;
        }
    }

    /**
     * Delete image and cleanup file.
     *
     * @param  int  $imageId
     * @return bool
     */
    public function deleteImage(int $imageId): bool
    {
        try {
            $image = $this->imageRepository->find($imageId);
            
            if (! $image) {
                return false;
            }
            
            $productId = $image->product_id;
            
            // Delete from database (model event will handle file cleanup)
            $success = $this->imageRepository->deleteAndReorder($imageId);
            
            if ($success) {
                $this->invalidateCache($productId);
            }
            
            return $success;
        } catch (\Exception $e) {
            \Log::error('Failed to delete 360 image', [
                'error' => $e->getMessage(),
                'image_id' => $imageId,
            ]);
            
            return false;
        }
    }

    /**
     * Get images formatted for frontend viewer.
     *
     * @param  int  $productId
     * @return array
     */
    public function getImagesForViewer(int $productId): array
    {
        $cacheEnabled = config('product360.cache.enabled', true);
        $cacheTtl = config('product360.cache.ttl', 3600);
        $cacheKey = $this->getCacheKey($productId);
        
        if ($cacheEnabled) {
            return Cache::remember($cacheKey, $cacheTtl, function () use ($productId) {
                return $this->fetchImagesForViewer($productId);
            });
        }
        
        return $this->fetchImagesForViewer($productId);
    }

    /**
     * Cleanup orphaned files.
     * 
     * Continues processing even if individual file deletions fail.
     * Returns count of successfully deleted files.
     *
     * @return int Number of files cleaned up
     */
    public function cleanupOrphanedFiles(): int
    {
        try {
            $orphanedFiles = $this->imageRepository->findOrphanedFiles();
            $disk = config('product360.storage.disk', 'public');
            $deletedCount = 0;
            $failedCount = 0;
            
            foreach ($orphanedFiles as $filePath) {
                try {
                    if ($this->imageService->delete($filePath)) {
                        $deletedCount++;
                    } else {
                        $failedCount++;
                        \Log::warning('Failed to delete orphaned file', [
                            'path' => $filePath,
                        ]);
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    \Log::error('Exception while deleting orphaned file', [
                        'path' => $filePath,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            \Log::info('Cleaned up orphaned 360 image files', [
                'deleted_count' => $deletedCount,
                'failed_count' => $failedCount,
                'total_found' => count($orphanedFiles),
            ]);
            
            return $deletedCount;
        } catch (\Exception $e) {
            \Log::error('Failed to cleanup orphaned files', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return 0;
        }
    }

    /**
     * Fetch images for viewer (without cache).
     * Executes a single optimized query using composite index.
     *
     * @param  int  $productId
     * @return array
     */
    protected function fetchImagesForViewer(int $productId): array
    {
        // Single query with explicit column selection for performance
        $images = $this->imageRepository->getByProduct($productId);
        
        return $images->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => $image->url,
                'position' => $image->position,
            ];
        })->toArray();
    }

    /**
     * Get cache key for product images.
     *
     * @param  int  $productId
     * @return string
     */
    protected function getCacheKey(int $productId): string
    {
        $prefix = config('product360.cache.prefix', 'product_360_images');
        
        return $prefix . '_' . $productId;
    }

    /**
     * Invalidate cache for product images.
     *
     * @param  int  $productId
     * @return void
     */
    protected function invalidateCache(int $productId): void
    {
        $cacheKey = $this->getCacheKey($productId);
        Cache::forget($cacheKey);
    }
}
