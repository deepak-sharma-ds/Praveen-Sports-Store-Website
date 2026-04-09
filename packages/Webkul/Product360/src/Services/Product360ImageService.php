<?php

namespace Webkul\Product360\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Product360ImageService
 * 
 * Handles file storage operations for Product 360 images.
 * Manages file uploads, deletions, and storage path generation.
 */
class Product360ImageService
{
    /**
     * Store uploaded file and return path.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  int  $productId
     * @return string
     * @throws \Exception
     */
    public function store(UploadedFile $file, int $productId): string
    {
        $disk = config('product360.storage.disk', 'public');
        $directory = $this->getStorageDirectory($productId);
        
        try {
            // Ensure storage directory exists
            if (! Storage::disk($disk)->exists($directory)) {
                Storage::disk($disk)->makeDirectory($directory);
            }
            
            // Generate unique filename
            $filename = $this->generateFilename($file);
            $path = $directory . '/' . $filename;
            
            // Check if WebP conversion is enabled
            $convertToWebP = config('product360.performance.convert_to_webp', true);
            
            if ($convertToWebP) {
                // Convert to WebP and store
                $this->storeAsWebP($file, $disk, $directory, $filename);
            } else {
                // Store the file as-is
                Storage::disk($disk)->putFileAs($directory, $file, $filename);
            }
            
            return $path;
        } catch (\Exception $e) {
            \Log::error('Failed to store 360 image file', [
                'product_id' => $productId,
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new \Exception('Failed to save image to storage. Please check disk space and permissions.');
        }
    }

    /**
     * Delete file from storage.
     * 
     * Catches all exceptions and logs errors without throwing.
     * Returns true if file was deleted or doesn't exist.
     * Returns false only if deletion failed.
     *
     * @param  string  $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        $disk = config('product360.storage.disk', 'public');
        
        try {
            if (Storage::disk($disk)->exists($path)) {
                $deleted = Storage::disk($disk)->delete($path);
                
                if ($deleted) {
                    \Log::info('Successfully deleted 360 image file', [
                        'path' => $path,
                    ]);
                    return true;
                } else {
                    \Log::warning('File deletion returned false but did not throw exception', [
                        'path' => $path,
                    ]);
                    return false;
                }
            }
            
            // File doesn't exist - consider this success
            \Log::info('360 image file not found during deletion (already deleted)', [
                'path' => $path,
            ]);
            return true;
        } catch (\Exception $e) {
            // Log error but don't throw exception - allow processing to continue
            \Log::error('Failed to delete 360 image file', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return false;
        }
    }

    /**
     * Store image as WebP format with conversion.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $disk
     * @param  string  $directory
     * @param  string  $filename
     * @return void
     * @throws \Exception
     */
    protected function storeAsWebP(UploadedFile $file, string $disk, string $directory, string $filename): void
    {
        // Change extension to .webp
        $webpFilename = preg_replace('/\.[^.]+$/', '.webp', $filename);
        $fullPath = storage_path('app/public/' . $directory . '/' . $webpFilename);
        
        // Ensure directory exists in absolute path
        $absoluteDirectory = storage_path('app/public/' . $directory);
        if (!file_exists($absoluteDirectory)) {
            mkdir($absoluteDirectory, 0755, true);
        }
        
        // Get WebP quality from config
        $quality = config('product360.performance.webp_quality', 85);
        
        // Use Intervention Image to convert and save as WebP
        $image = \Intervention\Image\Facades\Image::make($file->getRealPath());
        
        // Maintain original aspect ratio (no resizing, just format conversion)
        $image->encode('webp', $quality);
        
        // Save to storage
        $image->save($fullPath);
    }

    /**
     * Generate unique filename using random hash.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $hash = Str::random(40);
        
        // If WebP conversion is enabled, always use .webp extension
        $convertToWebP = config('product360.performance.convert_to_webp', true);
        
        if ($convertToWebP) {
            return $hash . '.webp';
        }
        
        // Otherwise use original extension
        $extension = $file->getClientOriginalExtension();
        return $hash . '.' . $extension;
    }

    /**
     * Get storage directory for product.
     *
     * @param  int  $productId
     * @return string
     */
    protected function getStorageDirectory(int $productId): string
    {
        $basePath = config('product360.storage.path', 'product-360-images');
        
        return $basePath . '/' . $productId;
    }
}
