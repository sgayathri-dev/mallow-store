<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    public function lowStock(Request $request)
    {
        $threshold = $request->input('threshold', 10);

        $products = Product::where('stock_on_hand', '<', $threshold)
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'data' => $products,
        ]);
    }
}