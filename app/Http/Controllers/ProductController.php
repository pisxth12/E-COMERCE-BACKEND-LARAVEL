<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request){
        try{
            $products = Product::with(['categories','options'])
                                ->search($request->search)
                                ->priceRange($request->min_price , $request->max_price)
                                ->searchCategory($request->category_id)
                                ->when($request->in_stock, function ($query){
                                    $query->inStock();
                                });




            return response()->json([
                'success'=>true,
                'data' => $products,
                'message' => 'Products retrieved successfully.'

            ]);
            
        }catch(\Exception $e){
            Log::error('Product index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products.',
                'error' => config('app.debug') ? $e->getMessage() : null

            ],500);
        }
    }

    public function store(StoreProductRequest $request  ){
        try{
            $product = Product::create($request->validated());
            
            if($request->has('category_ids')){
                $product->categories()->attach($request->get('category_ids'));

            }

            if($request->has('option_ids')){
                $product->options()->attach($request->get('option_ids'));
            }

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product created successfully.'
            ],201);


        }catch(\Exception $e){
            Log::error('Product store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ],500);
        }
    }

    public function show($id){
        try{
            $product = Product::with('categories','options')->find($id);
            if(!$product){
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ],404);
            }

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product retrieved successfully.'
            ]);

        }catch(\Exception $e){
            Log::error('Product show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message',
                'error'=>config('app.debug')?$e->getMessage():null
            ],500);
        }

    }

    public function update($id,StoreProductRequest $request){
        try{
            $product = Product::find($id);
            if(!$product){  
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'

                ],404);
            }

            $product->update($request->validated());
     
            //Sync categories and options if provided
            if($request->has('category_ids')){
                $product->categories()->sync($request->get('category_ids'));
            }

            if($request->has('option_ids')){
                $product->options()->sync($request->get('option_ids'));
            }

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product updated successfully.'
            ]);

            
            
        }catch(\Exception $e){
            Log::error('Product update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product.',
                'error' => config('app.debug')?$e->getMessage():null
                ],500);
        }
    }

    public function destroy($id){
        try{
            $product = Product::find($id);
            if(!$product){
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ],404);                    
            }
                
                $product->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted successfully.'
                ]);


        }catch(\Exception $e){
            Log::error('Product destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product.',
                'error' => config('app.debug')?$e->getMessage():null
            ],500);
        }
    
                     
    }


}
