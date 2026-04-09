<?php

namespace Webkul\Product360\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Webkul\Product\Models\ProductProxy;
use Webkul\Product360\Contracts\Product360Image as Product360ImageContract;

class Product360Image extends Model implements Product360ImageContract
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_360_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'path',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'product_id' => 'integer',
        'position'   => 'integer',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Webkul\Product360\Database\Factories\Product360ImageFactory::new();
    }

    /**
     * Get the product that owns the 360 image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Get the URL attribute for the image.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        $disk = config('product360.storage.disk', 'public');
        
        return Storage::disk($disk)->url($this->path);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Handle file cleanup when model is deleted
         */
        static::deleting(function ($image) {
            $disk = config('product360.storage.disk', 'public');
            
            try {
                if (Storage::disk($disk)->exists($image->path)) {
                    $deleted = Storage::disk($disk)->delete($image->path);
                    
                    if ($deleted) {
                        \Log::info('Successfully deleted 360 image file', [
                            'image_id' => $image->id,
                            'path' => $image->path,
                        ]);
                    } else {
                        \Log::warning('File deletion returned false but did not throw exception', [
                            'image_id' => $image->id,
                            'path' => $image->path,
                        ]);
                    }
                } else {
                    \Log::warning('360 image file not found during deletion', [
                        'image_id' => $image->id,
                        'path' => $image->path,
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but don't throw exception to allow deletion to continue
                // This ensures database record is always deleted even if file deletion fails
                \Log::error('Failed to delete 360 image file', [
                    'image_id' => $image->id,
                    'path' => $image->path,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });
    }
}
