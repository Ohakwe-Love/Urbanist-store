# Urbanist E-commerce: Complete Development Guide

This guide will walk you through building your modern furniture e-commerce platform "Urbanist" using Laravel 12, from initial setup to deployment. Since you're new to Laravel with a two-week timeline, we'll focus on practical solutions while covering all required features.

## Table of Contents
1. [Project Setup](#project-setup)
2. [Database Design](#database-design)
3. [User Authentication](#user-authentication)
4. [Product Management](#product-management)
5. [Shopping Cart](#shopping-cart)
6. [Checkout & Payment](#checkout-payment)
7. [Order Management](#order-management)
8. [Admin Dashboard](#admin-dashboard)
9. [Email Notifications](#email-notifications)
10. [Additional Features](#additional-features)
11. [Testing & Optimization](#testing-optimization)
12. [Deployment](#deployment)

## Project Setup

### Installing Laravel 12

```bash
# Install Laravel installer
composer global require laravel/installer

# Create new Laravel project
laravel new urbanist

# Navigate to project folder
cd urbanist
```

### Configure Environment Variables
Copy `.env.example` to `.env` and update database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=urbanist
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Install Required Packages

```bash
# Authentication scaffolding
composer require laravel/ui
php artisan ui bootstrap --auth

# Payment gateway integration
composer require paypalcheckoutsdk/paypal-checkout-sdk

# Google authentication
composer require laravel/socialite

# reCAPTCHA
composer require arcanedev/no-captcha

# Image handling
composer require intervention/image

# Mailer
composer require symfony/mailer
```

### Update package.json for frontend assets

```bash
npm install
npm run dev
```

## Database Design

### Create Migrations

Run the following commands to generate migration files:

```bash
# Users table (created with auth scaffolding)
# Add additional columns to users migration

# Products table
php artisan make:migration create_products_table

# Categories table
php artisan make:migration create_categories_table

# Product variants (size, color)
php artisan make:migration create_product_variants_table

# Inventory tracking
php artisan make:migration create_inventories_table

# Cart and cart items
php artisan make:migration create_carts_table
php artisan make:migration create_cart_items_table

# Orders and order items
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table

# Shipping details
php artisan make:migration create_shipping_details_table

# Payment details
php artisan make:migration create_payment_details_table
```

### Define Migration Structures

#### users table (modify existing migration)
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('google_id')->nullable();
    $table->string('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('zip_code')->nullable();
    $table->string('phone')->nullable();
    $table->boolean('is_admin')->default(false);
    $table->rememberToken();
    $table->timestamps();
});
```

#### products table
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->decimal('compare_price', 10, 2)->nullable();
    $table->string('main_image');
    $table->boolean('featured')->default(false);
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

#### categories table
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

#### product_variants table
```php
Schema::create('product_variants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('size')->nullable();
    $table->string('color')->nullable();
    $table->string('sku')->unique();
    $table->decimal('price_adjustment', 10, 2)->default(0);
    $table->string('image')->nullable();
    $table->timestamps();
});
```

#### inventories table
```php
Schema::create('inventories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
    $table->integer('quantity');
    $table->integer('low_stock_threshold')->default(5);
    $table->timestamps();
});
```

#### carts table
```php
Schema::create('carts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('session_id')->nullable();
    $table->timestamps();
});
```

#### cart_items table
```php
Schema::create('cart_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cart_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();
});
```

#### orders table
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('order_number')->unique();
    $table->enum('status', ['pending', 'processing', 'completed', 'declined', 'cancelled'])->default('pending');
    $table->decimal('subtotal', 10, 2);
    $table->decimal('tax', 10, 2);
    $table->decimal('shipping', 10, 2);
    $table->decimal('total', 10, 2);
    $table->string('payment_method');
    $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
    $table->timestamps();
});
```

#### order_items table
```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
    $table->string('product_name');
    $table->string('variant_details')->nullable();
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();
});
```

#### shipping_details table
```php
Schema::create('shipping_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email');
    $table->string('phone');
    $table->string('address');
    $table->string('city');
    $table->string('state');
    $table->string('zip_code');
    $table->string('country');
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

#### payment_details table
```php
Schema::create('payment_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->string('transaction_id')->nullable();
    $table->string('payment_method');
    $table->decimal('amount', 10, 2);
    $table->string('currency')->default('USD');
    $table->text('payment_data')->nullable();
    $table->timestamps();
});
```

### Run Migrations

```bash
php artisan migrate
```

### Create Models

Generate model files with relationships:

```bash
php artisan make:model User -m
php artisan make:model Product -m
php artisan make:model Category -m
php artisan make:model ProductVariant -m
php artisan make:model Inventory -m
php artisan make:model Cart -m
php artisan make:model CartItem -m
php artisan make:model Order -m
php artisan make:model OrderItem -m
php artisan make:model ShippingDetail -m
php artisan make:model PaymentDetail -m
```

### Define Model Relationships

Here's an example for the Product model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'main_image',
        'featured',
        'active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    // Generate slug from name
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }
}
```

Similarly, define relationships for all other models.

## User Authentication

### Basic Authentication (Already scaffolded with Laravel UI)

Customize the login and registration forms to match your design.

### Google Authentication

1. Create a Google OAuth client ID and secret at the [Google Developer Console](https://console.developers.google.com/)

2. Add credentials to `.env` file:
```
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT=http://localhost:8000/login/google/callback
```

3. Configure `config/services.php`:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT')
],
```

4. Create routes:
```php
// routes/web.php
Route::get('login/google', 'Auth\LoginController@redirectToGoogle');
Route::get('login/google/callback', 'Auth\LoginController@handleGoogleCallback');
```

5. Add methods to `LoginController`:
```php
// app/Http/Controllers/Auth/LoginController.php

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

public function redirectToGoogle()
{
    return Socialite::driver('google')->redirect();
}

public function handleGoogleCallback()
{
    try {
        $user = Socialite::driver('google')->user();
        
        $findUser = User::where('google_id', $user->id)->orWhere('email', $user->email)->first();
        
        if ($findUser) {
            // Update google_id if user exists but doesn't have google_id
            if (empty($findUser->google_id)) {
                $findUser->google_id = $user->id;
                $findUser->save();
            }
            
            Auth::login($findUser);
            return redirect('/home');
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'password' => bcrypt(str_random(16))
            ]);
            
            Auth::login($newUser);
            return redirect('/home');
        }
    } catch (\Exception $e) {
        return redirect('login')->with('error', 'Google login failed');
    }
}
```

### reCAPTCHA Integration

1. Get reCAPTCHA keys from [Google reCAPTCHA](https://www.google.com/recaptcha/admin)

2. Add to `.env` file:
```
NOCAPTCHA_SECRET=your_secret_key
NOCAPTCHA_SITEKEY=your_site_key
```

3. Add reCAPTCHA to login form:
```html
<!-- resources/views/auth/login.blade.php -->
<div class="form-group">
    {!! NoCaptcha::renderJs() !!}
    {!! NoCaptcha::display() !!}
    @error('g-recaptcha-response')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
```

4. Validate reCAPTCHA in LoginController:
```php
// app/Http/Controllers/Auth/LoginController.php

protected function validateLogin(Request $request)
{
    $request->validate([
        $this->username() => 'required|string',
        'password' => 'required|string',
        'g-recaptcha-response' => 'required|captcha'
    ]);
}
```

### Forgot Password

Laravel's built-in forgot password functionality should be sufficient. Ensure email configuration is set up correctly in your `.env` file:

```
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@urbanist.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Product Management

### Create Controllers

```bash
php artisan make:controller Admin/ProductController --resource
php artisan make:controller Admin/CategoryController --resource
php artisan make:controller Admin/InventoryController --resource
```

### Create Admin Product CRUD

Example for ProductController:

```php
// app/Http/Controllers/Admin/ProductController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::with('category')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'main_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'featured' => 'boolean',
            'active' => 'boolean',
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('main_image')) {
            $image = $request->file('main_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            // Save original
            $image->storeAs('products', $filename, 'public');
            
            // Create and save thumbnail
            $thumbnail = Image::make($image)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            
            $thumbnail->save(public_path('storage/products/thumbnails/' . $filename));
            
            $data['main_image'] = $filename;
        }
        
        // Generate slug
        $data['slug'] = Str::slug($request->name);
        
        $product = Product::create($data);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    // Other controller methods (show, edit, update, destroy)...
}
```

### Admin Product Views

Create views for product management in `resources/views/admin/products/`:

- index.blade.php (list all products)
- create.blade.php (form to create new product)
- edit.blade.php (form to edit product)
- show.blade.php (show product details)

### Set Up Admin Middleware

```php
// app/Http/Middleware/IsAdmin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && Auth::user()->is_admin) {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'You do not have admin access');
    }
}
```

Register the middleware in `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // Other middlewares...
    'admin' => \App\Http\Middleware\IsAdmin::class,
];
```

### Front-end Product Display

Create controllers and views for displaying products to customers:

```bash
php artisan make:controller ShopController
```

```php
// app/Http/Controllers/ShopController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('active', true);
        
        // Filter by category if provided
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
        }
        
        // Filter by price range if provided
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }
        
        // Sort products
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        $categories = Category::where('active', true)->get();
        
        return view('shop.index', compact('products', 'categories'));
    }
    
    public function show($slug)
    {
        $product = Product::with(['category', 'variants.inventory'])
            ->where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();
            
        return view('shop.show', compact('product'));
    }
}
```

## Shopping Cart

### Create CartController

```bash
php artisan make:controller CartController
```

```php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        $cartItems = CartItem::with('productVariant.product')
            ->where('cart_id', $cart->id)
            ->get();
            
        return view('cart.index', compact('cartItems', 'cart'));
    }
    
    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cart = $this->getCart();
        $productVariant = ProductVariant::with('product', 'inventory')->findOrFail($request->product_variant_id);
        
        // Check if item is in stock
        if ($productVariant->inventory->quantity < $request->quantity) {
            return back()->with('error', 'Not enough items in stock.');
        }
        
        // Check if item already exists in cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();
            
        if ($existingItem) {
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price' => $productVariant->product->price + $productVariant->price_adjustment,
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', 'Item added to cart.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getCart();
        
        // Ensure item belongs to current cart
        if ($cartItem->cart_id !== $cart->id) {
            return back()->with('error', 'Item does not belong to your cart.');
        }
        
        // Check inventory
        $productVariant = ProductVariant::with('inventory')->findOrFail($cartItem->product_variant_id);
        
        if ($productVariant->inventory->quantity < $request->quantity) {
            return back()->with('error', 'Not enough items in stock.');
        }
        
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        return back()->with('success', 'Cart updated.');
    }
    
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getCart();
        
        // Ensure item belongs to current cart
        if ($cartItem->cart_id !== $cart->id) {
            return back()->with('error', 'Item does not belong to your cart.');
        }
        
        $cartItem->delete();
        
        return back()->with('success', 'Item removed from cart.');
    }
    
    public function clear()
    {
        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)->delete();
        
        return back()->with('success', 'Cart cleared.');
    }
    
    private function getCart()
    {
        if (Auth::check()) {
            // Find or create cart for logged in user
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null]
            );
            
            // If user had a session cart, merge items
            if (session()->has('cart_id')) {
                $sessionCart = Cart::where('session_id', session('cart_id'))->first();
                
                if ($sessionCart) {
                    // Update cart items to new cart
                    CartItem::where('cart_id', $sessionCart->id)->update(['cart_id' => $cart->id]);
                    
                    // Delete session cart
                    $sessionCart->delete();
                }
                
                session()->forget('cart_id');
            }
            
            return $cart;
        } else {
            // Guest user with session cart
            if (session()->has('cart_id')) {
                $cart = Cart::where('session_id', session('cart_id'))->first();
                
                if ($cart) {
                    return $cart;
                }
            }
            
            // Create new session cart
            $sessionId = Str::uuid();
            session(['cart_id' => $sessionId]);
            
            return Cart::create(['session_id' => $sessionId]);
        }
    }
}
```

### Create Cart Views

Create views in `resources/views/cart/`:

```html
<!-- resources/views/cart/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Shopping Cart</h1>
    
    @if(count($cartItems) > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/products/thumbnails/' . $item->productVariant->product->main_image) }}" 
                                 alt="{{ $item->productVariant->product->name }}" width="50">
                            {{ $item->productVariant->product->name }}
                        </td>
                        <td>
                            @if($item->productVariant->size)
                                Size: {{ $item->productVariant->size }}<br>
                            @endif
                            @if($item->productVariant->color)
                                Color: {{ $item->productVariant->color }}
                            @endif
                        </td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="input-group">
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control" style="max-width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                                </div>
                            </form>
                        </td>
                        <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                        <td>${{ number_format($cartItems->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="d-flex justify-content-between">
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning">Clear Cart</button>
            </form>
            
            <a href="{{ route('checkout.index') }}" class="btn btn-primary">Proceed to Checkout</a>
        </div>
    @else
        <div class="alert alert-info">
            Your cart is empty. <a href="{{ route('shop.index') }}">Continue shopping</a>
        </div>
    @endif
</div>
@endsection
```

### Define Routes for Cart

```php
// routes/web.php

// Cart Routes
Route::get('/cart', 'CartController@index')->name('cart.index');
Route::post('/cart/add', 'CartController@add')->name('cart.add');
Route::put('/cart/update/{id}', 'CartController@update')->name('cart.update');
Route::delete('/cart/remove/{id}', 'CartController@remove')->name('cart.remove');
Route::post('/cart/clear', 'CartController@clear')->name('cart.clear');
```

## Checkout & Payment

### Create CheckoutController

```bash
php artisan make:controller CheckoutController
```

```php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingDetail;
use App\Models\PaymentDetail;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['guestCheckout']);
    }
    
    public function index()
    {
        $cart = $this->getCart();
        
        if (!$cart || CartItem::where('cart_id', $cart->id)->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }
        
        $cartItems = CartItem::with('productVariant.product')
            ->where('cart_id', $cart->id)
            ->get();
            
        $subtotal = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        $tax = $subtotal * 0.07; // 7% tax rate
        $shipping = 15.00; // Flat shipping rate
        $total = $subtotal + $tax + $shipping;
        
        return view('checkout.index', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique',


            ## Order Management

### Create OrderController

```bash
php artisan make:controller Admin/OrderController --resource
```

```php
// app/Http/Controllers/Admin/OrderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdated;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $query = Order::with('user');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by payment status if provided
        if ($request->has('payment_status') && $request->payment_status != 'all') {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Search by order number or customer name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show order details.
     */
    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.productVariant.product', 'shippingDetail', 'paymentDetail'])
            ->findOrFail($id);
            
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order.
     */
    public function edit($id)
    {
        $order = Order::with(['user', 'orderItems.productVariant.product', 'shippingDetail', 'paymentDetail'])
            ->findOrFail($id);
            
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the order.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,declined,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);
        
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        
        $order->status = $request->status;
        $order->payment_status = $request->payment_status;
        $order->save();
        
        // Send email notification if status changed
        if ($oldStatus != $request->status) {
            Mail::to($order->user->email)->send(new OrderStatusUpdated($order));
        }
        
        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Order updated successfully.');
    }
    
    /**
     * Generate invoice PDF for the order.
     */
    public function invoice($id)
    {
        $order = Order::with(['user', 'orderItems.productVariant.product', 'shippingDetail', 'paymentDetail'])
            ->findOrFail($id);
        
        // Generate PDF with invoice
        // In real implementation, you'd use a package like barryvdh/laravel-dompdf
        // For this example, we'll just return a view
        
        return view('admin.orders.invoice', compact('order'));
    }
}
```

### Create User Order Controller

To allow customers to view their order history:

```bash
php artisan make:controller OrderController
```

```php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display the user's order history.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('orders.index', compact('orders'));
    }
    
    /**
     * Display the specified order.
     */
    public function show($orderNumber)
    {
        $order = Order::with(['orderItems.productVariant.product', 'shippingDetail', 'paymentDetail'])
            ->where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        return view('orders.show', compact('order'));
    }
}
```

### Create Order Status# Urbanist E-commerce: Complete Development Guide

This guide will walk you through building your modern furniture e-commerce platform "Urbanist" using Laravel 12, from initial setup to deployment. Since you're new to Laravel with a two-week timeline, we'll focus on practical solutions while covering all required features.

## Table of Contents
1. [Project Setup](#project-setup)
2. [Database Design](#database-design)
3. [User Authentication](#user-authentication)
4. [Product Management](#product-management)
5. [Shopping Cart](#shopping-cart)
6. [Checkout & Payment](#checkout-payment)
7. [Order Management](#order-management)
8. [Admin Dashboard](#admin-dashboard)
9. [Email Notifications](#email-notifications)
10. [Additional Features](#additional-features)
11. [Testing & Optimization](#testing-optimization)
12. [Deployment](#deployment)

## Project Setup

### Installing Laravel 12

```bash
# Install Laravel installer
composer global require laravel/installer

# Create new Laravel project
laravel new urbanist

# Navigate to project folder
cd urbanist
```

### Configure Environment Variables
Copy `.env.example` to `.env` and update database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=urbanist
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Install Required Packages

```bash
# Authentication scaffolding
composer require laravel/ui
php artisan ui bootstrap --auth

# Payment gateway integration
composer require paypalcheckoutsdk/paypal-checkout-sdk

# Google authentication
composer require laravel/socialite

# reCAPTCHA
composer require arcanedev/no-captcha

# Image handling
composer require intervention/image

# Mailer
composer require symfony/mailer
```

### Update package.json for frontend assets

```bash
npm install
npm run dev
```

## Database Design

### Create Migrations

Run the following commands to generate migration files:

```bash
# Users table (created with auth scaffolding)
# Add additional columns to users migration

# Products table
php artisan make:migration create_products_table

# Categories table
php artisan make:migration create_categories_table

# Product variants (size, color)
php artisan make:migration create_product_variants_table

# Inventory tracking
php artisan make:migration create_inventories_table

# Cart and cart items
php artisan make:migration create_carts_table
php artisan make:migration create_cart_items_table

# Orders and order items
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table

# Shipping details
php artisan make:migration create_shipping_details_table

# Payment details
php artisan make:migration create_payment_details_table
```

### Define Migration Structures

#### users table (modify existing migration)
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('google_id')->nullable();
    $table->string('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('zip_code')->nullable();
    $table->string('phone')->nullable();
    $table->boolean('is_admin')->default(false);
    $table->rememberToken();
    $table->timestamps();
});
```

#### products table
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->decimal('compare_price', 10, 2)->nullable();
    $table->string('main_image');
    $table->boolean('featured')->default(false);
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

#### categories table
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

#### product_variants table
```php
Schema::create('product_variants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('size')->nullable();
    $table->string('color')->nullable();
    $table->string('sku')->unique();
    $table->decimal('price_adjustment', 10, 2)->default(0);
    $table->string('image')->nullable();
    $table->timestamps();
});
```

#### inventories table
```php
Schema::create('inventories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
    $table->integer('quantity');
    $table->integer('low_stock_threshold')->default(5);
    $table->timestamps();
});
```

#### carts table
```php
Schema::create('carts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('session_id')->nullable();
    $table->timestamps();
});
```

#### cart_items table
```php
Schema::create('cart_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cart_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();
});
```

#### orders table
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('order_number')->unique();
    $table->enum('status', ['pending', 'processing', 'completed', 'declined', 'cancelled'])->default('pending');
    $table->decimal('subtotal', 10, 2);
    $table->decimal('tax', 10, 2);
    $table->decimal('shipping', 10, 2);
    $table->decimal('total', 10, 2);
    $table->string('payment_method');
    $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
    $table->timestamps();
});
```

#### order_items table
```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
    $table->string('product_name');
    $table->string('variant_details')->nullable();
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();
});
```

#### shipping_details table
```php
Schema::create('shipping_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email');
    $table->string('phone');
    $table->string('address');
    $table->string('city');
    $table->string('state');
    $table->string('zip_code');
    $table->string('country');
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

#### payment_details table
```php
Schema::create('payment_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->string('transaction_id')->nullable();
    $table->string('payment_method');
    $table->decimal('amount', 10, 2);
    $table->string('currency')->default('USD');
    $table->text('payment_data')->nullable();
    $table->timestamps();
});
```

### Run Migrations

```bash
php artisan migrate
```

### Create Models

Generate model files with relationships:

```bash
php artisan make:model User -m
php artisan make:model Product -m
php artisan make:model Category -m
php artisan make:model ProductVariant -m
php artisan make:model Inventory -m
php artisan make:model Cart -m
php artisan make:model CartItem -m
php artisan make:model Order -m
php artisan make:model OrderItem -m
php artisan make:model ShippingDetail -m
php artisan make:model PaymentDetail -m
```

### Define Model Relationships

Here's an example for the Product model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'main_image',
        'featured',
        'active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    // Generate slug from name
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }
}
```

Similarly, define relationships for all other models.

## User Authentication

### Basic Authentication (Already scaffolded with Laravel UI)

Customize the login and registration forms to match your design.

### Google Authentication

1. Create a Google OAuth client ID and secret at the [Google Developer Console](https://console.developers.google.com/)

2. Add credentials to `.env` file:
```
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT=http://localhost:8000/login/google/callback
```

3. Configure `config/services.php`:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT')
],
```

4. Create routes:
```php
// routes/web.php
Route::get('login/google', 'Auth\LoginController@redirectToGoogle');
Route::get('login/google/callback', 'Auth\LoginController@handleGoogleCallback');
```

5. Add methods to `LoginController`:
```php
// app/Http/Controllers/Auth/LoginController.php

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

public function redirectToGoogle()
{
    return Socialite::driver('google')->redirect();
}

public function handleGoogleCallback()
{
    try {
        $user = Socialite::driver('google')->user();
        
        $findUser = User::where('google_id', $user->id)->orWhere('email', $user->email)->first();
        
        if ($findUser) {
            // Update google_id if user exists but doesn't have google_id
            if (empty($findUser->google_id)) {
                $findUser->google_id = $user->id;
                $findUser->save();
            }
            
            Auth::login($findUser);
            return redirect('/home');
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'password' => bcrypt(str_random(16))
            ]);
            
            Auth::login($newUser);
            return redirect('/home');
        }
    } catch (\Exception $e) {
        return redirect('login')->with('error', 'Google login failed');
    }
}
```

### reCAPTCHA Integration

1. Get reCAPTCHA keys from [Google reCAPTCHA](https://www.google.com/recaptcha/admin)

2. Add to `.env` file:
```
NOCAPTCHA_SECRET=your_secret_key
NOCAPTCHA_SITEKEY=your_site_key
```

3. Add reCAPTCHA to login form:
```html
<!-- resources/views/auth/login.blade.php -->
<div class="form-group">
    {!! NoCaptcha::renderJs() !!}
    {!! NoCaptcha::display() !!}
    @error('g-recaptcha-response')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
```

4. Validate reCAPTCHA in LoginController:
```php
// app/Http/Controllers/Auth/LoginController.php

protected function validateLogin(Request $request)
{
    $request->validate([
        $this->username() => 'required|string',
        'password' => 'required|string',
        'g-recaptcha-response' => 'required|captcha'
    ]);
}
```

### Forgot Password

Laravel's built-in forgot password functionality should be sufficient. Ensure email configuration is set up correctly in your `.env` file:

```
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@urbanist.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Product Management

### Create Controllers

```bash
php artisan make:controller Admin/ProductController --resource
php artisan make:controller Admin/CategoryController --resource
php artisan make:controller Admin/InventoryController --resource
```

### Create Admin Product CRUD

Example for ProductController:

```php
// app/Http/Controllers/Admin/ProductController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::with('category')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'main_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'featured' => 'boolean',
            'active' => 'boolean',
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('main_image')) {
            $image = $request->file('main_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            // Save original
            $image->storeAs('products', $filename, 'public');
            
            // Create and save thumbnail
            $thumbnail = Image::make($image)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            
            $thumbnail->save(public_path('storage/products/thumbnails/' . $filename));
            
            $data['main_image'] = $filename;
        }
        
        // Generate slug
        $data['slug'] = Str::slug($request->name);
        
        $product = Product::create($data);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    // Other controller methods (show, edit, update, destroy)...
}
```

### Admin Product Views

Create views for product management in `resources/views/admin/products/`:

- index.blade.php (list all products)
- create.blade.php (form to create new product)
- edit.blade.php (form to edit product)
- show.blade.php (show product details)

### Set Up Admin Middleware

```php
// app/Http/Middleware/IsAdmin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && Auth::user()->is_admin) {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'You do not have admin access');
    }
}
```

Register the middleware in `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // Other middlewares...
    'admin' => \App\Http\Middleware\IsAdmin::class,
];
```

### Front-end Product Display

Create controllers and views for displaying products to customers:

```bash
php artisan make:controller ShopController
```

```php
// app/Http/Controllers/ShopController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('active', true);
        
        // Filter by category if provided
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
        }
        
        // Filter by price range if provided
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }
        
        // Sort products
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        $categories = Category::where('active', true)->get();
        
        return view('shop.index', compact('products', 'categories'));
    }
    
    public function show($slug)
    {
        $product = Product::with(['category', 'variants.inventory'])
            ->where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();
            
        return view('shop.show', compact('product'));
    }
}
```

## Shopping Cart

### Create CartController

```bash
php artisan make:controller CartController
```

```php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        $cartItems = CartItem::with('productVariant.product')
            ->where('cart_id', $cart->id)
            ->get();
            
        return view('cart.index', compact('cartItems', 'cart'));
    }
    
    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cart = $this->getCart();
        $productVariant = ProductVariant::with('product', 'inventory')->findOrFail($request->product_variant_id);
        
        // Check if item is in stock
        if ($productVariant->inventory->quantity < $request->quantity) {
            return back()->with('error', 'Not enough items in stock.');
        }
        
        // Check if item already exists in cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();
            
        if ($existingItem) {
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price' => $productVariant->product->price + $productVariant->price_adjustment,
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', 'Item added to cart.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getCart();
        
        // Ensure item belongs to current cart
        if ($cartItem->cart_id !== $cart->id) {
            return back()->with('error', 'Item does not belong to your cart.');
        }
        
        // Check inventory
        $productVariant = ProductVariant::with('inventory')->findOrFail($cartItem->product_variant_id);
        
        if ($productVariant->inventory->quantity < $request->quantity) {
            return back()->with('error', 'Not enough items in stock.');
        }
        
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        return back()->with('success', 'Cart updated.');
    }
    
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getCart();
        
        // Ensure item belongs to current cart
        if ($cartItem->cart_id !== $cart->id) {
            return back()->with('error', 'Item does not belong to your cart.');
        }
        
        $cartItem->delete();
        
        return back()->with('success', 'Item removed from cart.');
    }
    
    public function clear()
    {
        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)->delete();
        
        return back()->with('success', 'Cart cleared.');
    }
    
    private function getCart()
    {
        if (Auth::check()) {
            // Find or create cart for logged in user
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null]
            );
            
            // If user had a session cart, merge items
            if (session()->has('cart_id')) {
                $sessionCart = Cart::where('session_id', session('cart_id'))->first();
                
                if ($sessionCart) {
                    // Update cart items to new cart
                    CartItem::where('cart_id', $sessionCart->id)->update(['cart_id' => $cart->id]);
                    
                    // Delete session cart
                    $sessionCart->delete();
                }
                
                session()->forget('cart_id');
            }
            
            return $cart;
        } else {
            // Guest user with session cart
            if (session()->has('cart_id')) {
                $cart = Cart::where('session_id', session('cart_id'))->first();
                
                if ($cart) {
                    return $cart;
                }
            }
            
            // Create new session cart
            $sessionId = Str::uuid();
            session(['cart_id' => $sessionId]);
            
            return Cart::create(['session_id' => $sessionId]);
        }
    }
}
```

### Create Cart Views

Create views in `resources/views/cart/`:

```html
<!-- resources/views/cart/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Shopping Cart</h1>
    
    @if(count($cartItems) > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/products/thumbnails/' . $item->productVariant->product->main_image) }}" 
                                 alt="{{ $item->productVariant->product->name }}" width="50">
                            {{ $item->productVariant->product->name }}
                        </td>
                        <td>
                            @if($item->productVariant->size)
                                Size: {{ $item->productVariant->size }}<br>
                            @endif
                            @if($item->productVariant->color)
                                Color: {{ $item->productVariant->color }}
                            @endif
                        </td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="input-group">
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control" style="max-width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                                </div>
                            </form>
                        </td>
                        <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                        <td>${{ number_format($cartItems->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="d-flex justify-content-between">
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning">Clear Cart</button>
            </form>
            
            <a href="{{ route('checkout.index') }}" class="btn btn-primary">Proceed to Checkout</a>
        </div>
    @else
        <div class="alert alert-info">
            Your cart is empty. <a href="{{ route('shop.index') }}">Continue shopping</a>
        </div>
    @endif
</div>
@endsection
```

### Create Checkout Views

Create views in `resources/views/checkout/`:

```html
<!-- resources/views/checkout/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Checkout</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Shipping Information</div>
                <div class="card-body">
                    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', Auth::user()->name) }}" required>
                                    @error('first_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', Auth::user()->phone) }}" required>
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', Auth::user()->address) }}" required>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', Auth::user()->city) }}" required>
                                    @error('city')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="state">State/Province</label>
                                    <input type="text" name="state" id="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', Auth::user()->state) }}" required>
                                    @error('state')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="zip_code">ZIP/Postal Code</label>
                                    <input type="text" name="zip_code" id="zip_code" class="form-control @error('zip_code') is-invalid @enderror" value="{{ old('zip_code', Auth::user()->zip_code) }}" required>
                                    @error('zip_code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', 'United States') }}" required>
                                    @error('country')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Order Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">Payment Method</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="paypal" name="payment_method" value="paypal" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="paypal">PayPal</label>
                                    </div>
                                    
                                    <div class="custom-control custom-radio mt-2">
                                        <input type="radio" id="credit_card" name="payment_method" value="credit_card" class="custom-control-input">
                                        <label class="custom-control-label" for="credit_card">Credit Card</label>
                                    </div>
                                    
                                    <div class="custom-control custom-radio mt-2">
                                        <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" class="custom-control-input">
                                        <label class="custom-control-label" for="bank_transfer">Bank Transfer</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg mt-4">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Order Summary</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            @foreach($cartItems as $item)
                            <tr>
                                <td>
                                    {{ $item->productVariant->product->name }} 
                                    <small>
                                        @if($item->productVariant->size) (Size: {{ $item->productVariant->size }}) @endif
                                        @if($item->productVariant->color) (Color: {{ $item->productVariant->color }}) @endif
                                    </small>
                                    <br>
                                    <span class="text-muted">Qty: {{ $item->quantity }}</span>
                                </td>
                                <td class="text-right">${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Subtotal</td>
                                <td class="text-right">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Tax (7%)</td>
                                <td class="text-right">${{ number_format($tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Shipping</td>
                                <td class="text-right">${{ number_format($shipping, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="text-right"><strong>${{ number_format($total, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

```html
<!-- resources/views/checkout/success.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Order Confirmed!</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fa fa-check-circle text-success" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Thank you for your order!</h5>
                        <p>Your order has been placed and is being processed.</p>
                        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                    </div>
                    
                    <h5>Order Details</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Order Date:</th>
                                <td>{{ $order->created_at->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td>{{ ucfirst($order->payment_method) }}</td>
                            </tr>
                            <tr>
                                <th>Payment Status:</th>
                                <td>
                                    @if($order->payment_status == 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-warning">{{ ucfirst($order->payment_status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Order Status:</th>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($order->status) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <h5>Order Items</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td>
                                    {{ $item->product_name }}
                                    @if($item->variant_details)
                                        <small>({{ $item->variant_details }})</small>
                                    @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">Subtotal:</td>
                                <td>${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">Tax:</td>
                                <td>${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">Shipping:</td>
                                <td>${{ number_format($order->shipping, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <h5>Shipping Address</h5>
                    <address>
                        {{ $order->shippingDetail->first_name }} {{ $order->shippingDetail->last_name }}<br>
                        {{ $order->shippingDetail->address }}<br>
                        {{ $order->shippingDetail->city }}, {{ $order->shippingDetail->state }} {{ $order->shippingDetail->zip_code }}<br>
                        {{ $order->shippingDetail->country }}<br>
                        <strong>Email:</strong> {{ $order->shippingDetail->email }}<br>
                        <strong>Phone:</strong> {{ $order->shippingDetail->phone }}
                    </address>
                    
                    @if($order->payment_method == 'bank_transfer')
                    <div class="alert alert-info mt-4">
                        <h5>Bank Transfer Information</h5>
                        <p>Please transfer the total amount to the following bank account:</p>
                        <p>
                            <strong>Bank Name:</strong> Example Bank<br>
                            <strong>Account Name:</strong> Urbanist Furniture<br>
                            <strong>Account Number:</strong> 1234567890<br>
                            <strong>Routing Number:</strong> 123456789<br>
                            <strong>Reference:</strong> {{ $order->order_number }}
                        </p>
                        <p class="mb-0">Your order will be processed once payment is received.</p>
                    </div>
                    @endif
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('shop.index') }}" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

## Checkout & Payment

### Create CheckoutController

```bash
php artisan make:controller CheckoutController
```

```php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingDetail;
use App\Models\PaymentDetail;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['guestCheckout']);
    }
    
    public function index()
    {
        $cart = $this->getCart();
        
        if (!$cart || CartItem::where('cart_id', $cart->id)->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }
        
        $cartItems = CartItem::with('productVariant.product')
            ->where('cart_id', $cart->id)
            ->get();
            
        $subtotal = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        $tax = $subtotal * 0.07; // 7% tax rate
        $shipping = 15.00; // Flat shipping rate
        $total = $subtotal + $tax + $shipping;
        
        return view('checkout.index', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'payment_method' => 'required|in:paypal,credit_card,bank_transfer',
        ]);
        
        $cart = $this->getCart();
        
        if (!$cart || CartItem::where('cart_id', $cart->id)->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }
        
        $cartItems = CartItem::with('productVariant.product', 'productVariant.inventory')
            ->where('cart_id', $cart->id)
            ->get();
            
        // Check inventory before proceeding
        foreach ($cartItems as $item) {
            if ($item->productVariant->inventory->quantity < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Sorry, {$item->productVariant->product->name} is out of stock.");
            }
        }
        
        $subtotal = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        $tax = $subtotal * 0.07; // 7% tax rate
        $shipping = 15.00; // Flat shipping rate
        $total = $subtotal + $tax + $shipping;
        
        DB::beginTransaction();
        
        try {
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);
            
            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->productVariant->product->name,
                    'variant_details' => $this->getVariantDetails($item->productVariant),
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
                
                // Update inventory
                $inventory = $item->productVariant->inventory;
                $inventory->quantity -= $item->quantity;
                $inventory->save();
            }
            
            // Create shipping details
            ShippingDetail::create([
                'order_id' => $order->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'notes' => $request->notes,
            ]);
            
            // Create payment details
            PaymentDetail::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $total,
                'currency' => 'USD',
            ]);
            
            // Clear the cart
            CartItem::where('cart_id', $cart->id)->delete();
            
            DB::commit();
            
            // Process payment based on method
            switch ($request->payment_method) {
                case 'paypal':
                    return $this->processPayPalPayment($order);
                case 'credit_card':
                    return $this->processCreditCardPayment($order, $request);
                case 'bank_transfer':
                    return $this->processBankTransferPayment($order);
                default:
                    throw new \Exception('Invalid payment method');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.index')
                ->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }
    
    private function getVariantDetails($variant)
    {
        $details = [];
        
        if ($variant->size) {
            $details[] = "Size: {$variant->size}";
        }
        
        if ($variant->color) {
            $details[] = "Color: {$variant->color}";
        }
        
        return implode(', ', $details);
    }
    
    private function processPayPalPayment($order)
    {
        // Here you would integrate with PayPal SDK
        // This is a simplified example
        
        // Redirect to thank you page for now
        return redirect()->route('checkout.success', ['order_number' => $order->order_number])
            ->with('success', 'Order placed successfully! We\'ll process your PayPal payment.');
    }
    
    private function processCreditCardPayment($order, $request)
    {
        // Here you would integrate with a payment processor
        // This is a simplified example
        
        // Update order payment status
        $order->payment_status = 'paid';
        $order->save();
        
        // Update payment details
        $paymentDetail = PaymentDetail::where('order_id', $order->id)->first();
        $paymentDetail->transaction_id = 'CC-' . strtoupper(Str::random(10));
        $paymentDetail->save();
        
        return redirect()->route('checkout.success', ['order_number' => $order->order_number])
            ->with('success', 'Order placed successfully! Your credit card payment has been processed.');
    }
    
    private function processBankTransferPayment($order)
    {
        // Redirect to thank you page with bank transfer instructions
        return redirect()->route('checkout.success', ['order_number' => $order->order_number])
            ->with('success', 'Order placed successfully! Please transfer the amount to our bank account within 3 days.');
    }
    
    public function success($order_number)
    {
        $order = Order::with(['orderItems.productVariant.product', 'shippingDetail', 'paymentDetail'])
            ->where('order_number', $order_number)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        return view('checkout.success', compact('order'));
    }
    
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            return Cart::where('session_id', session('cart_id'))->first();
        }
    }