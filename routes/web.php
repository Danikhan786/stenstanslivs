<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();
// Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('/register', [RegisterController::class, 'register']);


Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/login', [FrontendController::class, 'login'])->name('login');
Route::get('/shop', [FrontendController::class, 'shop'])->name('shop');
Route::get('/product/{id}', [FrontendController::class, 'product'])->name('product');
Route::get('/cart', [FrontendController::class, 'cart'])->name('cart');

Route::get('/cart/add/{id}', [FrontendController::class, 'addToCart'])->name('add.to.cart');
Route::post('/cart/update/{id}', [FrontendController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove/{id}', [FrontendController::class, 'removeFromCart'])->name('cart.remove');

Route::get('/checkout', [FrontendController::class, 'checkout'])->name('checkout');
Route::post('/checkout/store', [FrontendController::class, 'checkoutStore'])->name('checkout.store');
Route::get('/order-confirm', [FrontendController::class, 'orderComplete'])->name('orderComplete');

Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');

/*------------------------------------------
All Normal Users Routes List
--------------------------------------------*/
Route::middleware(['auth', 'user-access:user'])->group(function () {
  
    // Route::get('/', [FrontendController::class, 'index'])->name('index');
});

/*------------------------------------------
All Admin Routes List
--------------------------------------------
*/
Route::middleware(['auth', 'user-access:admin'])->group(function () {

    Route::get('/admin/home', [BackendController::class, 'adminHome'])->name('admin.home');

    Route::get('/admin/order', [BackendController::class, 'order'])->name('admin.order');
    
    // Category routes
    Route::get('/admin/category', [BackendController::class, 'category'])->name('category');
    Route::post('/categories', [BackendController::class, 'categoryStore'])->name('categories.store');
    Route::delete('/categories/{id}', [BackendController::class, 'categoryDestroy'])->name('categories.destroy');
    
    // Product routes
    Route::get('/admin/products', [ProductController::class, 'productIndex'])->name('products.index');
    Route::get('/admin/products/create', [ProductController::class, 'productCreate'])->name('products.create');
    Route::post('/admin/products', [ProductController::class, 'productStore'])->name('products.store');
    Route::get('/admin/products/edit/{id}', [ProductController::class, 'productEdit'])->name('products.edit');
    Route::put('/admin/products/{id}', [ProductController::class, 'ProductUpdate'])->name('products.update');
    Route::delete('/admin/products/{id}', [ProductController::class, 'productDestroy'])->name('products.destroy');
    
    Route::delete('/orders/{id}', [BackendController::class, 'orderDestroy'])->name('orders.destroy');

});

Route::post('/stripe/create-session', [StripeController::class, 'createSession'])->name('stripe.create.session');
Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');



