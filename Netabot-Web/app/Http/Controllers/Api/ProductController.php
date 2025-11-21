<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();
        return response()->json([
            'message' => 'success',
            'data' => $product,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'price'       => 'required|numeric',
            'description' => 'nullable|string',
            'url_images'  => 'nullable|string',  // SUDAH DIBENERIN
            'link'        => 'nullable|string',  // TAMBAH FIELD LINK
        ]);

        $product = Product::updateOrCreate(
            ['name' => $validated['name']],
            $validated
        );

        return response()->json([
            'message' => 'Product saved',
            'product' => $product
        ], 200);
    }
}
