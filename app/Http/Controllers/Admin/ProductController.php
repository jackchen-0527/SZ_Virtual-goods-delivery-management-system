<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    //
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->get();
        return view('admin.products', compact('products'));
    }
    // 产品列表
    public function list(Request $request)
    {
        $currentPage = $request->input('currentPage', 1);
        $pageSize = $request->input('pageSize', 10);
        $paginator = Product::paginate($pageSize);
        if ($paginator->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => '暂无产品数据',
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
    //添加产品
    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:190',
            'region' => 'required|string|max:500',
            'version' => 'required|string|max:190',
            'type' => 'required|integer',
            'decompress_password' => 'required|string|max:190',
            'file' => 'required|file|mimes:zip,rar,7z,tar,gz|max:102400',
        ]);
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/products', $fileName);
            $publicPath = Storage::url($filePath);
        } else {
            return response()->json(['error' => '未获取到有效的上传文件'], 400);
        }
        $data = $request->only('name', 'region', 'version', 'type', 'decompress_password');
        $data['file_path'] = $publicPath;
        $res_add = Product::create($data);
        return response()->json(['status' => 200, 'success' => '添加成功', 'data' => $data]);
    }
    //搜索产品
    public function search(Request $request)
    {
        $params = $request->post('params');
        $product_name = $params['product_name'] ?? '';
        $type = $params['type'] ?? 0;
        $date = $params['date'] ?? '';
        $currentPage = $request->post('currentPage', 1);
        $pageSize = $request->post('pageSize', 10);


        if (empty($product_name) && empty($type) && empty($date)) {
            return response()->json([
                'status' => 400,
                'message' => '请输入至少一个搜索条件',
                'data' => [],
                'total' => 0
            ]);
        }


        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });


        $query = Product::query();
        if (!empty($product_name)) {
            $query->where('name', 'like', '%' . $product_name . '%');
        }

        if (!empty($type)) {
            $query->where('type', $type);
        }

        if (!empty($date)) {
            $query->whereDate('created_at', $date);
        }

        $paginator = $query->latest()->paginate($pageSize);

        if ($paginator->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => '未找到符合条件的产品',
                'data' => [],
                'total' => 0
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => '搜索成功',
            'data' => $paginator->items(),
            'total' => $paginator->total()
        ]);
    }
}
