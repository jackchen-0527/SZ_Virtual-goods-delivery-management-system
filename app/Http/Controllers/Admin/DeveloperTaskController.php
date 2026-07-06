<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequireIssuesReport;
use Illuminate\Http\Request;

class DeveloperTaskController extends Controller
{
    //   
    public function index()
    {
        return view('admin.developer_task');
    }
    // 开发者需求任务列表
    public function list(Request $request)
    {
        $currentPage = $request->input('currentPage', 1);
        $pageSize = $request->input('pageSize', 10);
        $paginator = RequireIssuesReport::where('type', '1')->latest()->paginate($pageSize);
        if ($paginator->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => '暂无开发需求',
                'data' => [],
                'total' => 0
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => '获取开发需求列表成功',
            'data' => $paginator->items(),
            'total' => $paginator->total()
        ]);
    }
}
