<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AsyncQueueMonitoringController extends Controller
{
    //
    public function index()
    {
        return view('admin.async_queue_monitoring');
    }
    // 异步任务监控列表
    public function list()
    {

    }
}
