<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index(Request $request){
        try{
            $orders = Order::with(['customer', 'orderDetails.product'])
                ->search($request->search)
                ->byStatus($request->status)
                ->byCustomer($request->customer_id)
                ->dateRange($request->start_date, $request->end_date)
                ->when($request->recent, function ($query) use ($request) {
                    return $query->recent($request->recent_days ?? 7);
                })
                ->get();
            


        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
