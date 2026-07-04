<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAccessKey;
use Illuminate\Http\Request;

class StockAccessKeyController extends Controller
{
    //
    public function index()
    {
        return view('admin.stock_access_key');
    }
    //库存卡密列表
    public function list(Request $request)
    {
        $currentPage = $request->input('currentPage', 1);
        $pageSize = $request->input('pageSize', 10);
        $paginator = StockAccessKey::paginate($pageSize);
        if ($paginator->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => '暂无库存卡密数据',
                'data' => [],
                'total' => 0
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => '获取成功',
            'data' => $paginator->items(),
            'total' => $paginator->total()
        ]);
    }
    // 添加库存卡密
    public function add()
    {

    }
}
