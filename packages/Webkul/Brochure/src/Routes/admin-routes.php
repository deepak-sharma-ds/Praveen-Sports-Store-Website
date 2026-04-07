<?php

use Illuminate\Support\Facades\Route;
use Webkul\Brochure\Http\Controllers\Admin\BrochureController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/brochure'], function () {
    Route::controller(BrochureController::class)->group(function () {
        Route::get('', 'index')->name('admin.brochure.index');
        Route::get('create', 'create')->name('admin.brochure.create');
        Route::post('', 'store')->name('admin.brochure.store');
        Route::get('{id}/edit', 'edit')->name('admin.brochure.edit');
        Route::put('{id}', 'update')->name('admin.brochure.update');
        Route::delete('{id}', 'destroy')->name('admin.brochure.destroy');
        Route::post('mass-destroy', 'massDestroy')->name('admin.brochure.mass_destroy');
    });
});
