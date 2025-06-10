<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\UploadController;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
Route::middleware('admin.guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
});

// Admin Protected Routes
Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Products
    Route::resource('products', ProductController::class);
    
    // Attributes
    Route::resource('attributes', AttributeController::class);
    Route::get('attributes/{attribute}/options', [AttributeController::class, 'options'])->name('attributes.options');
    Route::post('attributes/{attribute}/options', [AttributeController::class, 'storeOption'])->name('attributes.options.store');
    Route::put('attributes/{attribute}/options/{option}', [AttributeController::class, 'updateOption'])->name('attributes.options.update');
    Route::delete('attributes/{attribute}/options/{option}', [AttributeController::class, 'destroyOption'])->name('attributes.options.destroy');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/{group}', [SettingsController::class, 'getByGroup'])->name('settings.group');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Image Upload Routes
    Route::post('/upload/image', [UploadController::class, 'uploadImage'])->name('upload.image');
    Route::delete('/upload/image', [UploadController::class, 'deleteImage'])->name('upload.image.delete');
});
