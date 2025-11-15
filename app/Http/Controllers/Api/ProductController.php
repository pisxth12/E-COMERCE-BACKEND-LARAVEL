<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        try {
            $query = Product::query();
            
            // Search functionality
            if ($request->has('search') && !empty($search)) {
                $query->search($search);
            }

            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->status($request->status);
            }

            // Filter by category
            if ($request->has('category') && !empty($request->category)) {
                $query->category($request->category);
            }

            // Filter by stock status
            if ($request->has('stock_status')) {
                if ($request->stock_status === 'in_stock') {
                    $query->where('stock', '>', 0);
                } elseif ($request->stock_status === 'out_of_stock') {
                    $query->where('stock', '<=', 0);
                }
            }

            // Sorting
            $sortField = $request->query('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $products = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved successfully',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    // Store product
    public function store(StoreProductRequest $request)
    {
        try {
            $productData = $request->validated();

            // Handle specifications json
            if ($request->has('specifications')) {
                $productData['specifications'] = $request->specifications;
            }

            // Auto-update status based on stock
            if ($productData['stock'] > 0) {
                $productData['status'] = 'active';
            } else {
                $productData['status'] = 'inactive';
            }

            // Create product
            $product = Product::create($productData);

            // Handle images
            if ($request->hasFile('images')) {
                $this->uploadProductImages($product, $request->file('images'), $request->primary_image_index);
            }

            $product->load('images');

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product stored successfully',
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Failed to store product',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Upload product images 
    private function uploadProductImages(Product $product, $images, $primaryImageIndex = null)
    {
        $existingImagesCount = $product->images()->count();
        
        foreach ($images as $index => $image) {
            $imagePath = $image->store('products', 'public');
            
            $imageData = [
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'sort_order' => $existingImagesCount + $index,
                'is_primary' => false
            ];

            // Set as primary if this is the specified index
            if ($primaryImageIndex !== null && $index == $primaryImageIndex) {
                // Remove primary from existing images
                $product->images()->update(['is_primary' => false]);
                $imageData['is_primary'] = true;
            }
            
            ProductImage::create($imageData);
        }

        // If no primary image is set and we have images, set first one as primary
        if ($primaryImageIndex === null && $existingImagesCount === 0 && count($images) > 0) {
            $firstImage = $product->images()->first();
            if ($firstImage) {
                $product->images()->update(['is_primary' => false]);
                $firstImage->update(['is_primary' => true]);
            }
        }
    }

    public function show(Product $product)
    {
        try {
            $product->load('images');
            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product retrieved successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Failed to retrieve product',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        try {
            $productData = $request->validated();

            // Handle specifications json
            if ($request->has('specifications')) {
                $productData['specifications'] = $request->specifications;
            }

            // Auto-update status based on stock
            if ($productData['stock'] > 0) {
                $productData['status'] = 'active';
            } else {
                $productData['status'] = 'inactive';
            }

            // Handle delete images
            if ($request->has('delete_image_ids')) {
                $this->deleteProductImages($product, $request->delete_image_ids);
            }

            // Handle new images
            if ($request->hasFile('images')) {
                $this->uploadProductImages($product, $request->file('images'), $request->primary_image_index);
            }

            // Update primary image if specified
            if ($request->has('primary_image_id')) {
                $this->setPrimaryImage($product, $request->primary_image_id);
            }

            $product->update($productData);
            $product->load('images');

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product updated successfully',
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Destroy product
    public function destroy(Product $product)
    {
        try {
            // Delete associated images
            $this->deleteAllProductImages($product);
            $product->delete();
            
            return response([
                'success' => true,
                'message' => 'Product deleted successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Delete specific product images
    private function deleteProductImages(Product $product, $imageIds)
    {
        $images = $product->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            // Delete from storage
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            // Delete from database
            $image->delete();
        }
    }

    // Delete all product images
    private function deleteAllProductImages(Product $product)
    {
        $images = $product->images()->get();

        foreach ($images as $image) {
            // Delete from storage
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            // Delete from database
            $image->delete();
        }
    }

    // Set primary image
    private function setPrimaryImage(Product $product, $imageId)
    {
        // Remove primary from existing images
        $product->images()->update(['is_primary' => false]);

        // Set new primary
        $product->images()->where('id', $imageId)->update(['is_primary' => true]);
    }

    // Bulk delete product
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:products,id'
            ]);

            $products = Product::whereIn('id', $request->ids)->get();
            
            foreach ($products as $product) {
                // Delete associated images
                $this->deleteAllProductImages($product);
                $product->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Products deleted successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete products',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    // Update product status
    public function updateStatus(Request $request, Product $product)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive'
            ]);
            
            $product->update([
                'status' => $request->status
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product status updated successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product status',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Get product categories
    public function categories()
    {
        try {
            $categories = Product::distinct()->pluck('category')->filter()->values();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Product categories retrieved successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product categories',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Search products
    public function search($search)
    {
        try {
            $products = Product::with('images')
                ->search($search)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,    
                'message' => 'Products retrieved successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Get product statistics
    public function statistics()
    {
        try {
            $totalProducts = Product::count();
            $activeProducts = Product::where('status', 'active')->count();
            $outOfStockProducts = Product::where('stock', '<=', 0)->count();
            $totalStock = Product::sum('stock');
            
            $categoryStats = Product::select('category', \DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'out_of_stock_products' => $outOfStockProducts,
                    'total_stock' => $totalStock,
                    'category_stats' => $categoryStats
                ],
                'message' => 'Statistics retrieved successfully',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}