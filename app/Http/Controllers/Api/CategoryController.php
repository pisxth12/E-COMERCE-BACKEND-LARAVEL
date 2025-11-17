<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try{
            $categories = Category::withCount('products')->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ], Response::HTTP_OK);
            
        }catch(\Exception $e){
            Log::error('Category index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Store new category with file upload
  public function store(Request $request)
{
    try{
        \Log::info('Category Store Request Received:', [
            'hasFile' => $request->hasFile('thumbnail'),
            'fileValid' => $request->file('thumbnail') ? $request->file('thumbnail')->isValid() : 'no file',
            'allData' => $request->except('thumbnail'), // Don't log file content
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if($validator->fails()){
            \Log::error('Category Validation Failed Details:', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->except('thumbnail'),
                'file_info' => $request->file('thumbnail') ? [
                    'size' => $request->file('thumbnail')->getSize(),
                    'mime_type' => $request->file('thumbnail')->getMimeType(),
                    'original_name' => $request->file('thumbnail')->getClientOriginalName(),
                    'is_valid' => $request->file('thumbnail')->isValid(),
                ] : 'No file received'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $thumbnailPath = null;
        
        // Handle file upload
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $thumbnailPath = $request->file('thumbnail')->store('categories', 'public');
            \Log::info('File stored successfully at: ' . $thumbnailPath);
        } else {
            \Log::warning('File not received or invalid for storage');
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'thumbnail' => $thumbnailPath,
        ]);

        // Load relationships for response
        $category->loadCount('products');

        \Log::info('Category created successfully:', ['id' => $category->id]);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully'
        ], Response::HTTP_CREATED);

    } catch(\Exception $e){
        Log::error('Category store error: ' . $e->getMessage());
        Log::error('Category store trace: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Failed to create category',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
   
    public function show($id)
    {
        try{
            $category = Category::withCount('products')->find($id);
            if(!$category){
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ], Response::HTTP_OK);

        }catch(\Exception $e){
            Log::error('Category show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Update the specified category with file upload
    public function update($id, Request $request)
    {
        try{
            $category = Category::find($id);
            if(!$category){
               return response()->json([
                  'success' => false,
                  'message' => 'Category not found.'
               ], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $updateData = [
                'name' => $request->name ?? $category->name,
                'description' => $request->description ?? $category->description,
            ];

            // Handle file upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($category->thumbnail) {
                    Storage::disk('public')->delete($category->thumbnail);
                }
                
                $thumbnailPath = $request->file('thumbnail')->store('categories', 'public');
                $updateData['thumbnail'] = $thumbnailPath;
            }

            $category->update($updateData);

            // Load relationships for response
            $category->loadCount('products');

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category updated successfully.'
            ]);

        }catch(\Exception $e){
            Log::error('Category update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Delete the specified category
    public function destroy($id)
    {
        try{
            $category = Category::withCount('products')->find($id);
            if(!$category){
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if category has products
            if($category->products_count > 0){
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category. It is being used by products.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Delete thumbnail file if exists
            if ($category->thumbnail) {
                Storage::disk('public')->delete($category->thumbnail);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ]);

        }catch(\Exception $e){
            Log::error('Category destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Search category by name
    public function search($search)
    {
        try{
            $categories = Category::withCount('products')
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories search completed successfully.'
            ]);

        }catch(\Exception $e){
            Log::error('Category search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search categories',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Get categories with product count (fixed version)
    public function getWithProducts()
    {
        try{
            $categories = Category::withCount('products')
                ->orderBy('products_count', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories with product count retrieved successfully.'
            ]);

        }catch(\Exception $e){
            Log::error('Category getWithProducts error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get categories with product count',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}