<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_360_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->string('path', 255);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            // Foreign key constraint with cascade delete
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');

            // Composite index for efficient querying
            $table->index(['product_id', 'position']);

            /**
             * Extension Point: Future 3D Model Support
             * 
             * To add 3D model support in the future, you can extend this table with:
             * 
             * $table->string('model_path', 255)->nullable()->after('path');
             * 
             * This field would store the path to 3D model files (e.g., .glb, .gltf, .obj)
             * The viewer_type configuration field determines which viewer to use:
             * - 'image_sequence': Uses the 'path' field for image frames
             * - '3d_model': Uses the 'model_path' field for 3D model files
             * - 'hybrid': Uses both fields to provide multiple viewing options
             * 
             * Additional fields for 3D model metadata could include:
             * - $table->string('model_format', 50)->nullable(); // glb, gltf, obj, etc.
             * - $table->unsignedInteger('model_size')->nullable(); // File size in bytes
             * - $table->json('model_metadata')->nullable(); // Additional model properties
             */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_360_images');
    }
};
