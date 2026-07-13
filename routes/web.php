<?php

use App\Http\Controllers\Admin\AsyncQueueMonitoringController;
use App\Http\Controllers\Admin\DeveloperTaskController;
use App\Http\Controllers\Admin\OnlineCustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RequireIssuesReportController;
use App\Http\Controllers\Admin\SalepersonTasksController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\StockAccessKeyController;
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
    // 库存卡密管理
    Route::get('/stock_access_key', [StockAccessKeyController::class, 'index'])->name('stock_access_key.index');
    Route::get('/stock_access_key/list', [StockAccessKeyController::class, 'list'])->name('stock_access_key.list');
    Route::post('/stock_access_key/add', [StockAccessKeyController::class, 'add'])->name('stock_access_key.add');

    // 需求与问题上报
    Route::get('/require_issues_report', [RequireIssuesReportController::class, 'index'])->name('require_issues_report.index');
    Route::get('/require_issues_report/list', [RequireIssuesReportController::class, 'list'])->name('require_issues_report.list');
    Route::post('/require_issues_report/add', [RequireIssuesReportController::class, 'add'])->name('require_issues_report.add');
    Route::post('/require_issues_report/search', [RequireIssuesReportController::class, 'search'])->name('require_issues_report.search');

    // 开发者任务列表
    Route::get('/developer_task', [DeveloperTaskController::class, 'index'])->name('developer_task.index');
    Route::get('/developer_task/list', [DeveloperTaskController::class, 'list'])->name('developer_task.list');
    // 销售员任务列表
    Route::get('/sale_person_tasks', [SalepersonTasksController::class, 'index'])->name('sale_person_tasks.index');
    Route::get('/sale_person_tasks/list', [SalepersonTasksController::class, 'list'])->name('sale_person_tasks.list');
    // 异步队列监控
    Route::get('/async_queue_monitoring', [AsyncQueueMonitoringController::class, 'index'])->name('async_queue_monitoring.index');
    Route::get('/async_queue_monitoring/list', [AsyncQueueMonitoringController::class, 'list'])->name('async_queue_monitoring.list');
    // 在线客服
    Route::get('/online_customer', [OnlineCustomerController::class, 'index'])->name('index');
    Route::post('/online_customer/send', [OnlineCustomerController::class, 'send'])->name('online_customer.send');
    Route::post('/online_customer/reveice', [OnlineCustomerController::class, 'reveice'])->name('online_customer.reveice');
});