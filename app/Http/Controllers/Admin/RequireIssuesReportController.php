<?php

namespace App\Http\Controllers\Admin;

use App\Models\RequireIssuesReport;
use Illuminate\Http\Request;

class RequireIssuesReportController
{
    //
    public function index()
    {
        return view('admin.require_issues_report');
    }
    //添加需求任务
    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|integer|max:3',
            'level' => 'required|integer|max:3',
            'sponsor' => 'required|string|max:50',
            'description' => 'required|string|max:500',
            // 'file' => 'required|file|mimes:zip,rar,7z,tar,gz|max:102400',
        ]);
        $data = $request->only('name', 'type', 'level', 'sponsor', 'description');
        $res_add = RequireIssuesReport::create($data);
        return response()->json(['status' => 200, 'success' => '添加成功', 'data' => $data]);
    }
    //需求任务列表
    public function list(Request $request)
    {
        $currentPage = $request->input('currentPage');
        $pageSize = $request->input('pageSize');
        $paginator = RequireIssuesReport::paginate($pageSize);
        if ($paginator->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => '暂无需求数据',
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
    // 搜索需求任务
    public function search()
    {

    }
}
