<?php

namespace App\Http\Controllers\Admin;

use App\Models\RequireIssuesReport;
use App\Models\SalepersonTasks;
use Illuminate\Http\Request;

class SalepersonTasksController
{
    //
    public function index()
    {
        return view('admin.sale-person-tasks');
    }
    // 销售员需求任务列表
    public function list(Request $request)
    {
        $currentPage = $request->input('currentPage', 1);
        $pageSize = $request->input('pageSize', 10);
        $paginator = RequireIssuesReport::where('type', '2')->latest()->paginate($pageSize);
        if ($paginator->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => '暂无销售需求',
                'data' => [],
                'total' => 0
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => '获取销售需求列表成功',
            'data' => $paginator->items(),
            'total' => $paginator->total()
        ]);
    }
}
