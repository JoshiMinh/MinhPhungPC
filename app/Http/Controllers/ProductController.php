<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products', compact('products'));
    }

    public function show(Request $request)
    {
        $product = Product::find($request->get('id'));
        return view('item', compact('product'));
    }
}
