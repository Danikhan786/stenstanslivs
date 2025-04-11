<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Function to display all products
    public function productIndex()
    {
        $products = Product::all();
        return view('backend.products.index', compact('products'));
    }

    // Function to show the form for creating a new product
    public function productCreate()
    {
        $categories = Category::all(); // Fetch all categories for the dropdown
        return view('backend.products.create', compact('categories'));
    }

    // Function to store a new product
    public function productStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'longDescription' => 'required|string',
            'shortDescription' => 'required|string|max:255',
            'productImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id', // category must exist
        ]);

        // Handle file upload for product image
        $imageName = time() . '.' . $request->productImage->extension();
        $request->productImage->move(public_path('images'), $imageName);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'longDescription' => $request->longDescription,
            'shortDescription' => $request->shortDescription,
            'productImage' => $imageName,
            'category_id' => $request->category_id, // Assign category
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    // Function to display the form for editing a product
    public function productEdit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all(); // Fetch categories for the dropdown
        return view('backend.products.edit', compact('product', 'categories'));
    }

    // Function to update the product
    public function ProductUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'longDescription' => 'required|string',
            'shortDescription' => 'required|string|max:255',
            'productImage' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id', // category must exist
        ]);

        // Handle file upload for product image
        if ($request->hasFile('productImage')) {
            $imageName = time() . '.' . $request->productImage->extension();
            $request->productImage->move(public_path('images'), $imageName);
            $product->productImage = $imageName;
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'longDescription' => $request->longDescription,
            'shortDescription' => $request->shortDescription,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    // Function to delete a product
    public function productDestroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}

