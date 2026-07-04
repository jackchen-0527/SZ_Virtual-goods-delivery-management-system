<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\User\DownloadController;
use App\Http\Controllers\APi\MenuController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/fetch/{token}', [DownloadController::class, 'download'])->name('user.download');

// 前后端数据交互接口
Route::get('/api/menus', [MenuController::class, 'index']);
Route::post('/api/menus/store', [MenuController::class, 'store']);


//后台路由管理组
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.products.index');
    });
    // 产品管理
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/list', [ProductController::class, 'list'])->name('products.list');
    Route::post('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/products/add', [ProductController::class, 'add'])->name('products.add');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    //发货管理
    Route::get('/shipment', [ShipmentController::class, 'index'])->name('shipment.index');
    Route::post('/shipment/generate', [ShipmentController::class, 'generate'])->name('shipment.generate');
});