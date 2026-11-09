<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class FoodController extends Controller
{
    public function index(Request $request)
    {
        // 1. Khởi tạo Query Builder
        $query = DB::table('products');

        // 2. Lọc theo danh mục (Category)
        if ($request->has('category') && $request->category != '') {
            $query->where('id_category', $request->category); 
        }

        // 3. Tìm kiếm gần đúng (Search)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where('name', 'like', '%' . $keyword . '%'); 
        }

        // 4. Thực thi truy vấn lấy dữ liệu
        $foods = $query->get();
        return view('food.index', compact('foods'));
    }

    public function showDetail($id)
    {
        $food = DB::table('products')->where('id_product', $id)->first();

        if (!$food) {
            abort(404, 'Không tìm thấy món ăn');
        }

        return view('food.detail', compact('food'));
    }
}