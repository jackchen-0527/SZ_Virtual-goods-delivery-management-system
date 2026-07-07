<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAccessKey;
use Illuminate\Http\Request;
use App\Jobs\GenerateStockAccessKeys;

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
    public function add(Request $request)
    {
        $request->validate([
            'target_product' => 'required|integer',
            'access_key_num' => 'required|integer|min:1|max:50000',
            'access_key_bit' => 'required|integer|in:16,32,64',
            'task_level' => 'required|string|in:normal,medium,urgent',
            'handling_method' => 'required|integer|in:1,2',
        ]);
        $productIdentifier = $request->input('target_product'); // 产品ID
        $access_key_num = $request->input('access_key_num'); // 需要生成的密钥量
        $bitSize = $request->input('access_key_bit');  // 密钥位数
        $task_level = $request->input('task_level', 'normal');//任务紧急程度
        GenerateStockAccessKeys::dispatch($productIdentifier, $access_key_num, $bitSize, $task_level);
        return response()->json([
            'status' => 200,
            'message' => "成功! 开始逐步生成{$access_key_num}个库存密钥产品"
        ]);
    }
}
