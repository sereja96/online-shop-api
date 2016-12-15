<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Response;

use App\Http\Requests;

class CategoryController extends Controller
{
    public function getPopularCategories($count = 0, $search = null)
    {
        $categories = Category::with('image', 'productCount')
            ->search($search)
            ->takeCount($count)
            ->get();

        return Response::success($categories);
    }

    public function getCategory($id)
    {
        $category = Category::withAll()
            ->whereInIds($id)
            ->first();

        if ($category) {
            return Response::success($category);
        }

        return Response::error('category_not_found');
    }
}
