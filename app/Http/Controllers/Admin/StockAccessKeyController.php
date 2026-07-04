<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockAccessKeyController extends Controller
{
    //
    public function index()
    {
        return view('admin.stock_access_key');
    }
    //库存卡密列表
    public function list()
    {

    }
}
