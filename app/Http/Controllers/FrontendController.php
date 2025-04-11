<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class FrontendController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $categories = Category::with('products')->get();
        return view("frontend.index", compact('products', 'categories'));
    }

    public function shop(Request $request)
    {
        $query = Product::query();

        // // Price range filter
        // if ($request->has('price_range')) {
        //     $maxPrice = (int)$request->input('price_range');
        //     $query->where('price', '<=', $maxPrice);
        // }
    
        // Get the products with pagination
        $products = $query->paginate(12); // Adjust the number of products per page as needed
        $categories = Category::all(); // Fetch all categories
    
        return view('frontend.shop', compact('products', 'categories'));
    }

    public function product($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all(); 
        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->take(8) // Limit to 8 related products
                                  ->get();
        return view("frontend.product", compact('product', 'categories', 'relatedProducts'));
    }

    public function cart()
    {
        $userId = auth()->id();
        $cart = [];
        
        if ($userId) {
            // Get cart items from database for logged in users
            $cartItems = Cart::with('product')
                            ->where('user_id', $userId)
                            ->get();
                            
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'image' => $item->product->productImage,
                    'quantity' => $item->quantity
                ];
            }
        } else {
            // Use session cart for non-logged in users
            $cart = Session::get('cart', []);
        }
        
        $subtotal = $this->calculateSubtotal($cart);
        return view('frontend.cart', compact('cart', 'subtotal'));
    }
    

    // Add product to cart
    public function addToCart($id)
    {
        $product = Product::findOrFail($id);
        $userId = auth()->id(); // Get logged in user ID
        
        // Store in database
        if ($userId) {
            // Check if product already exists in cart for this user
            $cartItem = Cart::where('user_id', $userId)
                           ->where('product_id', $id)
                           ->first();
            
            if ($cartItem) {
                // Update quantity if product already in cart
                $cartItem->increment('quantity');
            } else {
                // Create new cart item
                Cart::create([
                    'user_id' => $userId,
                    'product_id' => $id,
                    'quantity' => 1
                ]);
            }
        }
        
        // Also maintain session cart for non-logged in users
        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->productImage,
                'quantity' => 1
            ];
            
            $cartCount = session('cart_count', 0);
            session(['cart_count' => $cartCount + 1]);
        }
        
        Session::put('cart', $cart);
        
        return redirect()->route('cart')->with('success', "{$product->name} added to cart");
    }
    
    // Update Cart Quantity
    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = auth()->id();
        
        if ($userId) {
            // Update database cart
            Cart::where('user_id', $userId)
                ->where('product_id', $id)
                ->update(['quantity' => $request->input('quantity')]);
        }
        
        // Update session cart
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->input('quantity');
            session()->put('cart', $cart);
        }
        
        return redirect()->route('cart')->with('success', 'Cart updated successfully!');
    }
    // Remove item from cart
    public function removeFromCart($id)
    {
        $userId = auth()->id();
        
        if ($userId) {
            // Remove from database
            Cart::where('user_id', $userId)
                ->where('product_id', $id)
                ->delete();
        }
        
        // Remove from session
        $cart = Session::get('cart', []);
        unset($cart[$id]);
        Session::put('cart', $cart);
        
        return redirect()->route('cart')->with('success', 'Product removed from cart');
    }

    public function checkout()
    {
        // Calculate subtotal from the session cart
        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Pass the subtotal to the view
        return view('frontend.checkout', compact('subtotal'));
    }
    
    public function checkoutStore(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'mobile' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'order_notes' => 'nullable|string',
            'payment_method' => 'required|in:stripe',
        ]);

        try {
            DB::beginTransaction();

            // Get cart items
            $cart = Session::get('cart', []);
            
            if (empty($cart)) {
                return redirect()->back()->with('error', 'Your cart is empty');
            }

            // Calculate total amount
            $totalAmount = collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            // Set initial payment status
            $paymentStatus = 'Pending';
            $transactionId = null;

            // Handle Stripe Payment
            if ($request->payment_method === 'stripe') {
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    
                    $charge = \Stripe\Charge::create([
                        'amount' => round($totalAmount * 100), // Convert to cents
                        'currency' => 'usd',
                        'source' => $request->stripeToken,
                        'description' => 'Order payment for ' . $request->email,
                    ]);

                    if ($charge->status === 'succeeded') {
                        $paymentStatus = 'Paid';
                        $transactionId = $charge->id;
                    } else {
                        throw new \Exception('Payment failed');
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(), // Add user_id if logged in
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country,
                'postcode' => $request->postcode,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'order_notes' => $request->order_notes,
                'total_amount' => $totalAmount,
                'status' => 'Pending',
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'transaction_id' => $transactionId
            ]);

            // Create order items
            foreach ($cart as $productId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            // Clear cart after successful order
            Session::forget(['cart', 'cart_count']);

            DB::commit();
            return redirect()->route('order.complete')->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Order creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
    
    public function orderComplete()
    {
        return view("frontend.orderConfirmation");
    }
    public function contact()
    {
        return view("frontend.contact");
    }

    // Calculate the subtotal for the cart
    private function calculateSubtotal($cart)
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return number_format($subtotal, 2); // Format the subtotal to 2 decimal places
    }

    public function login()
    {
        return view("frontend.login");
    }
}



