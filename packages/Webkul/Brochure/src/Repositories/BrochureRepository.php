<?php

namespace Webkul\Brochure\Repositories;

use Illuminate\Support\Facades\Storage;
use Webkul\Brochure\Models\Brochure;
use Webkul\Core\Eloquent\Repository;

class BrochureRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Brochure::class;
    }

    /**
     * Find an active brochure by its slug.
     */
    public function findActiveBySlug(string $slug): ?Brochure
    {
        return $this->model->where('slug', $slug)
            ->where('status', true)
            ->first();
    }

    /**
     * Get all active brochures ordered by sort_order.
     */
    public function getActiveBrochures()
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Create a brochure with file upload handling.
     */
    public function createWithUpload(array $data, $pdfFile = null, array $pageImages = []): Brochure
    {
        if ($pdfFile) {
            $data['pdf_path'] = $pdfFile->store('brochure/pdf', 'public');
        }

        $data['slug'] = Brochure::generateUniqueSlug($data['title']);

        $brochure = $this->create($data);

        if (! empty($pageImages)) {
            $this->storePageImages($brochure, $pageImages);
        }

        return $brochure;
    }

    /**
     * Update a brochure with optional file replacement.
     */
    public function updateWithUpload(Brochure $brochure, array $data, $pdfFile = null, array $pageImages = []): Brochure
    {
        if ($pdfFile) {
            // Remove old PDF if it exists
            if ($brochure->pdf_path) {
                Storage::disk('public')->delete($brochure->pdf_path);
            }

            $data['pdf_path'] = $pdfFile->store('brochure/pdf', 'public');
        }

        // Regenerate slug only if title changed
        if (isset($data['title']) && $data['title'] !== $brochure->title) {
            $data['slug'] = Brochure::generateUniqueSlug($data['title'], $brochure->id);
        }

        $brochure->update($data);

        if (! empty($pageImages)) {
            $this->storePageImages($brochure, $pageImages);
        }

        return $brochure->fresh();
    }

    /**
     * Delete a brochure and its associated storage files.
     */
    public function deleteWithFiles(int $id): bool
    {
        $brochure = $this->find($id);

        if (! $brochure) {
            return false;
        }

        // Remove PDF file
        if ($brochure->pdf_path) {
            Storage::disk('public')->delete($brochure->pdf_path);
        }

        // Remove page images directory
        $pagesDir = $brochure->pages_directory;

        if (Storage::disk('public')->exists($pagesDir)) {
            Storage::disk('public')->deleteDirectory($pagesDir);
        }

        return $brochure->delete();
    }

    /**
     * Store and convert uploaded page images to WebP format.
     */
    protected function storePageImages(Brochure $brochure, array $pageImages): void
    {
        $dir = storage_path('app/public/' . $brochure->pages_directory);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        foreach ($pageImages as $index => $image) {
            $pageNumber = $index + 1;
            $destPath = $dir . '/page-' . $pageNumber . '.webp';

            // Convert to WebP if GD is available
            if (function_exists('imagewebp')) {
                $this->convertToWebP($image->getRealPath(), $destPath);
            } else {
                // Fallback: store as-is then rename
                $stored = $image->store($brochure->pages_directory, 'public');
                $fullStored = storage_path('app/public/' . $stored);
                rename($fullStored, $destPath);
            }
        }
    }

    /**
     * Convert any supported image to WebP using GD.
     * Max 300KB per page after conversion.
     */
    protected function convertToWebP(string $sourcePath, string $destPath): bool
    {
        $mime = mime_content_type($sourcePath);
        $image = null;

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (! $image) {
            return false;
        }

        // Use quality 80 to keep file size under ~300KB
        $result = imagewebp($image, $destPath, 80);
        imagedestroy($image);

        return $result;
    }
}
