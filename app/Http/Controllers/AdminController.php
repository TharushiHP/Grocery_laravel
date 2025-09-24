<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        // If already logged in as admin, redirect to dashboard
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        // If logged in as regular user, logout first
        if (Auth::check() && !Auth::user()->isAdmin()) {
            Auth::logout();
        }
        
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the user by email first
        $user = User::where('email', $request->email)->first();
        
        // Check if user exists and has admin role
        if (!$user || !$user->isAdmin()) {
            return back()->withErrors([
                'email' => 'Access denied. Admin credentials required.',
            ])->withInput($request->only('email'));
        }

        // Attempt authentication only if user is admin
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            
            // Force redirect to admin dashboard
            return redirect('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');
        $products = Product::paginate(10);

        return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'totalRevenue', 'products'));
    }

    public function showProduct(Product $product)
    {
        return response()->json($product);
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|string|max:50',
            'image' => 'nullable|url',
        ]);

        try {
            Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'stock_quantity' => 100, // Set default stock quantity
                'image' => $request->input('image', ''), // Use input() with default value
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|string|max:50',
            'image' => 'nullable|url',
        ]);

        try {
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'image' => $request->input('image', ''), // Use input() with default value
                // Keep existing stock_quantity unchanged during updates
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();
        return response()->json(['success' => true]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/admin/login')->with('status', 'You have been logged out successfully.');
    }
}