<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OptionController extends Controller
{
    // Display a listing of the options
    public function index()
    {
        try {
            $options = Option::with('products')->get();

            return response()->json([
                'success' => true,
                'data' => $options,
                'message' => 'Options retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Option index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve options.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Store a newly created option.
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'option_name' => 'required|string|max:255|unique:options',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors() // Fixed: errors() instead of getErrors()
                ], 422);
            }

            $option = Option::create([
                'option_name' => $request->option_name,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'data' => $option,
                'message' => 'Option created successfully.' // Changed from 'stored' to 'created'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Option store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create option.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Display the specified option.
    public function show($id)
    {
        try {
            $option = Option::with('products')->find($id);

            if (!$option) {
                return response()->json([
                    'success' => false,
                    'message' => 'Option not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $option,
                'message' => 'Option retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Option show error: ' . $e->getMessage());   
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve option.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Update the specified option.
    public function update($id, Request $request)
    {
        try {
            $option = Option::find($id);
            if (!$option) {
                return response()->json([
                    'success' => false,
                    'message' => 'Option not found.'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'option_name' => 'sometimes|required|string|max:255|unique:options,option_name,' . $id,
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors() // Fixed: errors() instead of getErrors()
                ], 422);
            }

            // Fixed: Use request data directly instead of $request->all()
            $option->update([
                'option_name' => $request->option_name ?? $option->option_name,
                'description' => $request->description ?? $option->description
            ]);

            return response()->json([
                'success' => true,
                'data' => $option,
                'message' => 'Option updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Option update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update option.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Remove the specified option.
    public function destroy($id)
    {
        try {
            $option = Option::find($id);

            if (!$option) {
                return response()->json([
                    'success' => false,
                    'message' => 'Option not found.'
                ], 404);
            }

            // Check if option is being used by products
            if ($option->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete option. It is being used by products.'
                ], 422);
            }

            $option->delete();

            return response()->json([
                'success' => true,
                'message' => 'Option deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Option destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete option.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Search options by name.
    public function search($search)
    {
        try {
            $options = Option::with('products')
                ->where('option_name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->get();

            return response()->json([
                'success' => true,
                'data' => $options,
                'message' => 'Options search completed successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Option search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search options.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Get options with product count.
    public function getWithProducts()
    {
        try {
            $options = Option::withCount('products')->get();

            return response()->json([
                'success' => true,
                'data' => $options,
                'message' => 'Options with product count retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Option getWithProducts error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get options with product count.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}