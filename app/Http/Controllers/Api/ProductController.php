<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends ApiController
{
    /**
     * Get all products with optional filtering.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::query();

            // Filter by category if provided
            if ($request->has('category') && !empty($request->category)) {
                $query->where('category', $request->category);
            }

            // Search by name or description if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Apply pagination
            $perPage = min($request->get('per_page', 15), 50); // Max 50 per page
            $products = $query->paginate($perPage);

            return $this->successResponse([
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem()
                ]
            ], 'Products retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve products', 500);
        }
    }

    /**
     * Get a specific product by ID.
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            return $this->successResponse($product, 'Product retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Product not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve product', 500);
        }
    }

    /**
     * Get all available categories.
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Product::select('category')
                ->distinct()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->orderBy('category')
                ->pluck('category');

            return $this->successResponse($categories, 'Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve categories', 500);
        }
    }

    /**
     * Create a new product (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
                'quantity' => 'required|string|max:50',
                'image' => 'nullable|url|max:500'
            ]);

            $validated['stock_quantity'] = 100; // Default stock

            $product = Product::create($validated);

            return $this->successResponse($product, 'Product created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create product', 500);
        }
    }

    /**
     * Update a product (Admin only).
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'category' => 'sometimes|string|max:100',
                'quantity' => 'sometimes|string|max:50',
                'image' => 'nullable|url|max:500'
            ]);

            $product->update($validated);

            return $this->successResponse($product, 'Product updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Product not found', 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update product', 500);
        }
    }

    /**
     * Delete a product (Admin only).
     */
    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return $this->successResponse(null, 'Product deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Product not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete product', 500);
        }
    }
}