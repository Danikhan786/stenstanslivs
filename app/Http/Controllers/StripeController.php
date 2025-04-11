<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class StripeController extends Controller
{
    public function createSession(Request $request)
    {
        $formData = $request->formData;
        
        // Get cart items from session
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }
        
        // Calculate total
        $total = 0;
        $lineItems = [];
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'sek',
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['price'] * 100, // Convert to cents
                ],
                'quantity' => $item['quantity'],
            ];
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),
                'metadata' => [
                    'order_data' => json_encode($formData)
                ],
                'currency' => 'sek', // Added explicit currency setting
                'locale' => 'sv', // Added Swedish locale
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            \Log::error('Stripe session creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Payment initialization failed'], 500);
        }
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        
        try {
            DB::beginTransaction();

            $session = Session::retrieve($request->session_id);
            $orderData = json_decode($session->metadata->order_data, true);
            
            // Get cart items
            $cart = session('cart', []);
            
            if (empty($cart)) {
                throw new \Exception('Cart is empty');
            }

            // Calculate total amount
            $totalAmount = collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(), // Will be null for guest users
                'first_name' => $orderData['first_name'],
                'last_name' => $orderData['last_name'],
                'email' => $orderData['email'],
                'mobile' => $orderData['mobile'],
                'address' => $orderData['address'],
                'city' => $orderData['city'],
                'country' => $orderData['country'],
                'postcode' => $orderData['postcode'],
                'order_notes' => $orderData['order_notes'] ?? null,
                'total_amount' => $totalAmount,
                'payment_method' => 'stripe',
                'payment_status' => 'Paid',
                'transaction_id' => $session->payment_intent,
                'status' => 'Pending'
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

            // Clear cart from database if user is logged in
            if (auth()->check()) {
                Cart::where('user_id', auth()->id())->delete();
            }

            // Clear session cart
            session()->forget(['cart', 'cart_count']);

            DB::commit();
            
            return redirect()->route('orderComplete')->with('success', 'Payment successful! Your order has been placed.');
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Order processing failed: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Payment successful but order processing failed. Please contact support.');
        }
    }

    public function cancel()
    {
        return redirect()->route('checkout')->with('error', 'Payment cancelled.');
    }
}



