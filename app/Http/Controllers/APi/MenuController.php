<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    //
    public function index()
    {
        $menus = Menu::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        if ($menus->isEmpty()) {
            $defaultMenu = Menu::create([
                'name' => '系统菜单管理',
                'icon' => 'Menu',
                'slug' => 'menu_manager',
                'sort_order' => 1
            ]);
            return response()->json([$defaultMenu]);
        }
        $menuTree = $this->buildTree($menus);
        return response()->json($menuTree);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:menus,slug',
            'parent_id' => 'int'
        ]);
        $menu = Menu::create($request->only(['parent_id', 'name', 'icon', 'slug']));
        return response()->json(['success' => true, 'data' => $menu]);
    }

    public function buildTree($menus, $parentId = 0)
    {
        $branch = array();
        foreach ($menus as $item) {
            if (isset($item['parent_id']) && $item['parent_id'] == $parentId) {
                $children = $this->buildTree($menus, $item['id']);
                $item['children'] = $children ? $children : [];
                $branch[] = $item;
            }
        }
        return $branch;
    }
}
