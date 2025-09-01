<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Order::sum('total_amount');
        $totalOrders = Order::count();
        return view('admin.dashboard', compact('totalRevenue', 'totalOrders'));
    }

    public function manageProducts(Request $request)
    {
        $searchQuery = $request->get('search');
        $products = Product::where('name', 'like', "%$searchQuery%")
            ->orWhere('brand', 'like', "%$searchQuery%")
            ->get();
        return view('admin.manage_products', compact('products', 'searchQuery'));
    }

    public function manageUsers(Request $request)
    {
        $searchQuery = $request->get('search');
        $users = User::where('name', 'like', "%$searchQuery%")
            ->orWhere('email', 'like', "%$searchQuery%")
            ->get();
        return view('admin.manage_users', compact('users', 'searchQuery'));
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.edit_product', compact('product'));
    }

    public function updateProduct(Request $request)
    {
        $product = Product::findOrFail($request->input('product_id'));
        $product->update($request->all());
        return redirect()->route('admin.products');
    }

    public function deleteProduct(Request $request)
    {
        $product = Product::findOrFail($request->input('product_id'));
        $product->delete();
        return redirect()->route('admin.products');
    }

    public function updateOrder(Request $request)
    {
        $order = Order::findOrFail($request->input('order_id'));
        $order->update($request->only(['status', 'payment_status']));
        return redirect()->route('admin.dashboard');
    }
}