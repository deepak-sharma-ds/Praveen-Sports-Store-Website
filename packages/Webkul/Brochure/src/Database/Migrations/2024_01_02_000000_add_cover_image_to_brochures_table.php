<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add cover_image column to brochures table.
     * Stores the path to an optional cover thumbnail image shown on the listing page.
     */
    public function up(): void
    {
        Schema::table('brochures', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('brochures', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
};
