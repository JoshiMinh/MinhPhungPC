<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        $results = Product::where('name', 'like', "%$query%")
            ->orWhere('brand', 'like', "%$query%")
            ->get();
        return view('search_result', compact('results', 'query'));
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }
}
