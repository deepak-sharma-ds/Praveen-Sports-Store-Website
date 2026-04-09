<?php

namespace Webkul\Product360\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\Product360\Http\Requests\Product360ReorderRequest;
use Webkul\Product360\Http\Requests\Product360UploadRequest;
use Webkul\Product360\Services\Product360Service;

class Product360Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product360\Services\Product360Service  $product360Service
     * @return void
     */
    public function __construct(
        protected Product360Service $product360Service
    ) {}

    /**
     * Get all 360 images for a product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $productId): JsonResponse
    {
        try {
            $images = $this->product360Service->getImagesForViewer($productId);

            return new JsonResponse([
                'success' => true,
                'data'    => [
                    'images' => $images,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve images: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload multiple 360 images for a product.
     *
     * @param  \Webkul\Product360\Http\Requests\Product360UploadRequest  $request
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Product360UploadRequest $request, int $productId): JsonResponse
    {
        try {
            $result = $this->product360Service->uploadImages(
                $request->file('images'),
                $productId
            );

            if ($result['success']) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Images uploaded successfully',
                    'data'    => [
                        'images' => $result['images'],
                    ],
                ], 201);
            }

            // Service returned failure
            \Log::error('Upload service returned failure', [
                'product_id' => $productId,
                'message' => $result['message'] ?? 'Unknown error',
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to upload images',
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in upload controller', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Reorder 360 images for a product.
     *
     * @param  \Webkul\Product360\Http\Requests\Product360ReorderRequest  $request
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Product360ReorderRequest $request, int $productId): JsonResponse
    {
        try {
            $success = $this->product360Service->reorderImages(
                $request->input('order'),
                $productId
            );

            if ($success) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Images reordered successfully',
                ]);
            }

            \Log::error('Reorder operation failed', [
                'product_id' => $productId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder images',
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in reorder controller', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder images. Please try again.',
            ], 500);
        }
    }

    /**
     * Delete a 360 image.
     *
     * @param  int  $productId
     * @param  int  $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(int $productId, int $imageId): JsonResponse
    {
        try {
            $success = $this->product360Service->deleteImage($imageId);

            if ($success) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Image deleted successfully',
                ]);
            }

            \Log::warning('Image not found for deletion', [
                'product_id' => $productId,
                'image_id' => $imageId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Image not found',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in delete controller', [
                'product_id' => $productId,
                'image_id' => $imageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image. Please try again.',
            ], 500);
        }
    }
}
