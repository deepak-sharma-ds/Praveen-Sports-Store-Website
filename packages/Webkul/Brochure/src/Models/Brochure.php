<?php

namespace Webkul\Brochure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Brochure extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'brochures';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'pdf_path',
        'cover_image',
        'type',
        'status',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status'     => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the PDF public URL.
     */
    public function getPdfUrlAttribute(): ?string
    {
        if (! $this->pdf_path) {
            return null;
        }

        return Storage::url($this->pdf_path);
    }

    /**
     * Get the cover image public URL.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        return Storage::url($this->cover_image);
    }

    /**
     * Get the directory for pre-rendered page images.
     * Images are stored as: brochure/pages/{slug}/page-{n}.webp
     */
    public function getPagesDirectoryAttribute(): string
    {
        return 'brochure/pages/' . $this->slug;
    }

    /**
     * Get the public URL for a specific page image.
     */
    public function getPageImageUrl(int $page): string
    {
        return Storage::url($this->pages_directory . '/page-' . $page . '.webp');
    }

    /**
     * Get all available page image URLs for image-mode brochures.
     */
    public function getPageImagesAttribute(): array
    {
        $dir = storage_path('app/public/' . $this->pages_directory);

        if (! is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/page-*.webp');
        $pages = [];

        if ($files) {
            usort($files, function ($a, $b) {
                preg_match('/page-(\d+)\.webp$/', $a, $ma);
                preg_match('/page-(\d+)\.webp$/', $b, $mb);

                return ($ma[1] ?? 0) <=> ($mb[1] ?? 0);
            });

            foreach ($files as $file) {
                $pages[] = Storage::url($this->pages_directory . '/' . basename($file));
            }
        }

        return $pages;
    }

    /**
     * Generate a unique slug from a given title.
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;

        while (true) {
            $query = static::where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Scope for active brochures only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered brochures.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }
}
