<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function search($search)
    {
        try {
            // If search is empty, return all products            
            if (empty($search) || $search === '') {
                $products = Product::with(['categories', 'options'])->get();
            } else {
                $products = Product::with(['categories', 'options'])
                    ->search($search)
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products search completed successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Product search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search products.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // filter by category
    public function filterByCategory($categoryId)
    {
        try {
            $products = Product::with(['categories', 'options'])
                ->byCategory($categoryId)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products by category retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Product filterByCategory error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to filter products by category.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $products = Product::with(['categories', 'options'])
                ->search($request->search)
                ->priceRange($request->min_price, $request->max_price)
                ->byCategory($request->category_id) // Fixed: changed from searchCategory to byCategory
                ->when($request->in_stock, function ($query) {
                    return $query->inStock(); // Fixed: added return
                })
                ->get(); // Fixed: added get() to execute the query

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Product index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = Product::create($request->validated());
            
            if ($request->has('category_ids')) {
                $product->categories()->attach($request->category_ids); // Fixed: removed get()
            }

            if ($request->has('option_ids')) {
                $product->options()->attach($request->option_ids); // Fixed: removed get()
            }

            // Load relationships for response
            $product->load(['categories', 'options']);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product created successfully.'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Product store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with(['categories', 'options'])->find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Product show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product.', // Fixed: added message text
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update($id, StoreProductRequest $request)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {  
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ], 404);
            }

            $product->update($request->validated());

            // Sync categories and options if provided
            if ($request->has('category_ids')) {
                $product->categories()->sync($request->category_ids); // Fixed: removed get()
            }

            if ($request->has('option_ids')) {
                $product->options()->sync($request->option_ids); // Fixed: removed get()
            }

            // Load updated relationships
            $product->load(['categories', 'options']);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Product update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ], 404);                    
            }
            
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Product destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }                   
    }
}