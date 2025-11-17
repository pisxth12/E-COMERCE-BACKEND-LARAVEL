<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CustomerController extends Controller
{
    public function index(Request $request){
        try{
            $customers = Customer::with('orders')
            ->search($request->search)
            ->byCountry($request->country)
            ->get();

            return response()->json([
                'success' => true,
                'data' => $customers,
                'message' => 'Customers retrieved successfully'
            ], Response::HTTP_OK);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customers',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
