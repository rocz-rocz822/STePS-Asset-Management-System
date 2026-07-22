<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AssetCodeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetHistoryController;
use App\Http\Controllers\BorrowRecordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    Route::get('/search', GlobalSearchController::class)
        ->name('search');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Asset Management
    |--------------------------------------------------------------------------
    | Static routes must come before Route::resource()
    |--------------------------------------------------------------------------
    */

    Route::get('assets/trashed', [AssetController::class, 'trashed'])
        ->name('assets.trashed');

    Route::patch('assets/{assetId}/restore', [AssetController::class, 'restore'])
        ->name('assets.restore');

    Route::resource('assets', AssetController::class);

    Route::delete(
        'assets/{asset}/attachments/{attachment}',
        [AssetController::class, 'deleteAttachment']
    )->name('assets.attachments.destroy');

    // Route::get(
    //     'assets/{asset}/qrcode',
    //     [AssetCodeController::class, 'qrCode']
    // )->name('assets.qrcode');

    // Route::get(
    //     'assets/{asset}/barcode',
    //     [AssetCodeController::class, 'barcode']
    // )->name('assets.barcode');

    /*  
    |--------------------------------------------------------------------------
    | Borrow Records
    |--------------------------------------------------------------------------
    */

    Route::resource('borrow-records', BorrowRecordController::class)
        ->except(['show', 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Maintenance Records
    |--------------------------------------------------------------------------
    */

    Route::resource('maintenance-records', MaintenanceRecordController::class)
        ->except(['show', 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Asset History & Activity Logs
    |--------------------------------------------------------------------------
    */

    Route::get(
        'asset-histories',
        [AssetHistoryController::class, 'index']
    )->name('asset-histories.index');

    Route::get(
        'activity-logs',
        [ActivityLogController::class, 'index']
    )->name('activity-logs.index');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Only administrators can access these routes.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */

    Route::resource('users', UserController::class)
        ->except(['show']);

    Route::patch(
        'users/{user}/toggle-status',
        [UserController::class, 'toggleStatus']
    )->name('users.toggle-status');

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    Route::resource('categories', CategoryController::class)
        ->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Locations
    |--------------------------------------------------------------------------
    */

    Route::resource('locations', LocationController::class)
        ->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get(
        'reports',
        [ReportController::class, 'index']
    )->name('reports.index');

    Route::get(
        'reports/{type}',
        [ReportController::class, 'show']
    )->name('reports.show');

    Route::get(
        'reports/{type}/export/{format}',
        [ReportController::class, 'export']
    )->name('reports.export');
});

require __DIR__.'/auth.php';