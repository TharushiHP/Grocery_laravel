<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Get all products with optional filtering
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Filter by category if provided
            if ($request->has('category') && $request->category !== '') {
                $query->where('category', 'like', '%' . $request->category . '%');
            }

            // Search by name if provided
            if ($request->has('search') && $request->search !== '') {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter by price range
            if ($request->has('min_price') && is_numeric($request->min_price)) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && is_numeric($request->max_price)) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filter by availability
            if ($request->has('in_stock') && $request->in_stock === 'true') {
                $query->where('quantity', '>', 0);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['name', 'price', 'category', 'quantity', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $perPage = min(max((int)$perPage, 1), 100); // Limit between 1-100

            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'products' => $products->items(),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'from' => $products->firstItem(),
                        'to' => $products->lastItem(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific product by ID
     */
    public function show($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => [
                    'product' => $product
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all unique categories
     */
    public function categories()
    {
        try {
            $categories = Product::select('category')
                                ->distinct()
                                ->whereNotNull('category')
                                ->where('category', '!=', '')
                                ->orderBy('category')
                                ->pluck('category');

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => [
                    'categories' => $categories
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by specific category
     */
    public function getByCategory($category)
    {
        try {
            $products = Product::where('category', $category)
                              ->orderBy('name')
                              ->get();

            return response()->json([
                'success' => true,
                'message' => "Products in '{$category}' category retrieved successfully",
                'data' => [
                    'category' => $category,
                    'products' => $products
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products by name
     */
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:1|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $searchQuery = $request->query;
            
            $products = Product::where('name', 'like', '%' . $searchQuery . '%')
                              ->orWhere('description', 'like', '%' . $searchQuery . '%')
                              ->orWhere('category', 'like', '%' . $searchQuery . '%')
                              ->orderBy('name')
                              ->get();

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => [
                    'search_query' => $searchQuery,
                    'results_count' => $products->count(),
                    'products' => $products
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured/popular products
     */
    public function featured(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $limit = min(max((int)$limit, 1), 50); // Limit between 1-50

            // For now, we'll return products ordered by creation date
            // You can modify this logic based on your business needs
            // (e.g., most ordered, highest rated, etc.)
            $products = Product::where('quantity', '>', 0)
                              ->orderBy('created_at', 'desc')
                              ->limit($limit)
                              ->get();

            return response()->json([
                'success' => true,
                'message' => 'Featured products retrieved successfully',
                'data' => [
                    'products' => $products
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve featured products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products with low stock
     */
    public function lowStock(Request $request)
    {
        try {
            $threshold = $request->get('threshold', 10);
            $threshold = max((int)$threshold, 1);

            $products = Product::where('quantity', '<=', $threshold)
                              ->where('quantity', '>', 0)
                              ->orderBy('quantity', 'asc')
                              ->get();

            return response()->json([
                'success' => true,
                'message' => 'Low stock products retrieved successfully',
                'data' => [
                    'threshold' => $threshold,
                    'products' => $products
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve low stock products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new product (admin functionality)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|max:255',
                'quantity' => 'required|integer|min:0',
                'image' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product = Product::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => [
                    'product' => $product
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing product (admin functionality)
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'category' => 'sometimes|required|string|max:255',
                'quantity' => 'sometimes|required|integer|min:0',
                'image' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => [
                    'product' => $product->fresh()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a product (admin functionality)
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}