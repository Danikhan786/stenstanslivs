<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\cart;
use App\Models\Product;

use Illuminate\Http\Request;

class BackendController extends Controller
{
    public function adminHome()
    {
        // Count the total number of products
    $productCount = Product::count();

    // Count the total number of orders
    $orderCount = Order::count();

    // Pass the counts to the view
    return view("backend.index", compact('productCount', 'orderCount'));
    }
    public function order()
    {
        $orders = Order::with(['items.product'])->get();
        return view("backend.order", compact('orders'));
    }
    public function category()
    {
        $categories = Category::all();
        return view("backend.category",compact('categories'));
    }

     // Function to create and store a category
     public function categoryStore(Request $request)
     {
         // Validate the request data
         $request->validate([
             'name' => 'required|string|max:255',
         ]);
 
         // Create and save the category
         Category::create([
             'name' => $request->name,
         ]);
 
         return redirect()->back()->with('success', 'Category added successfully!');
     }
 
     // Function to delete a category
     public function categoryDestroy($id)
     {
         // Find category by id and delete
         $category = Category::findOrFail($id);
         $category->delete();
 
         return redirect()->back()->with('success', 'Category deleted successfully!');
     }
    public function orderDestroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete(); // Delete related order items first
        $order->delete();

        return redirect()->back()->with('success', 'Order deleted successfully!');
    }
}


