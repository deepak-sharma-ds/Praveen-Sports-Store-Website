<?php

namespace Webkul\Product360\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\Repository;

class Product360ImageRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Webkul\Product360\Contracts\Product360Image';
    }

    /**
     * Get all images for a product, ordered by position.
     * Uses composite index (product_id, position) for optimal performance.
     *
     * @param  int  $productId
     * @return \Illuminate\Support\Collection
     */
    public function getByProduct(int $productId): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->orderBy('position', 'asc')
            ->get(['id', 'product_id', 'path', 'position', 'created_at', 'updated_at']);
    }

    /**
     * Get images for multiple products at once (for eager loading).
     * Uses composite index for optimal performance.
     *
     * @param  array  $productIds
     * @return \Illuminate\Support\Collection
     */
    public function getByProducts(array $productIds): Collection
    {
        return $this->model
            ->whereIn('product_id', $productIds)
            ->orderBy('product_id', 'asc')
            ->orderBy('position', 'asc')
            ->get(['id', 'product_id', 'path', 'position', 'created_at', 'updated_at']);
    }

    /**
     * Create multiple images from upload.
     *
     * @param  array  $imagesData
     * @param  int  $productId
     * @return \Illuminate\Support\Collection
     */
    public function createMany(array $imagesData, int $productId): Collection
    {
        $createdImages = collect();
        
        foreach ($imagesData as $imageData) {
            $image = $this->create([
                'product_id' => $productId,
                'path'       => $imageData['path'],
                'position'   => $imageData['position'],
            ]);
            
            $createdImages->push($image);
        }
        
        return $createdImages;
    }

    /**
     * Update position values for reordering.
     *
     * @param  array  $positionMap  Array of ['id' => imageId, 'position' => newPosition]
     * @return bool
     */
    public function updatePositions(array $positionMap): bool
    {
        try {
            DB::beginTransaction();
            
            foreach ($positionMap as $item) {
                $this->update([
                    'position' => $item['position'],
                ], $item['id']);
            }
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Failed to update image positions', [
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Delete image and recalculate positions for remaining images.
     *
     * @param  int  $imageId
     * @return bool
     */
    public function deleteAndReorder(int $imageId): bool
    {
        try {
            DB::beginTransaction();
            
            $image = $this->find($imageId);
            
            if (! $image) {
                DB::rollBack();
                return false;
            }
            
            $productId = $image->product_id;
            $deletedPosition = $image->position;
            
            // Delete the image (model event will handle file cleanup)
            $this->delete($imageId);
            
            // Recalculate positions for remaining images
            $this->model
                ->where('product_id', $productId)
                ->where('position', '>', $deletedPosition)
                ->decrement('position');
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Failed to delete and reorder images', [
                'error' => $e->getMessage(),
                'image_id' => $imageId,
            ]);
            
            return false;
        }
    }

    /**
     * Find orphaned files without database records.
     *
     * @return \Illuminate\Support\Collection
     */
    public function findOrphanedFiles(): Collection
    {
        $disk = config('product360.storage.disk', 'public');
        $directory = config('product360.storage.path', 'product-360-images');
        
        $orphanedFiles = collect();
        
        try {
            // Get all files from storage
            $allFiles = collect(Storage::disk($disk)->allFiles($directory));
            
            // Get all paths from database
            $databasePaths = $this->model->pluck('path');
            
            // Find files that don't have database records
            $orphanedFiles = $allFiles->diff($databasePaths);
        } catch (\Exception $e) {
            \Log::error('Failed to find orphaned files', [
                'error' => $e->getMessage(),
            ]);
        }
        
        return $orphanedFiles;
    }
}
