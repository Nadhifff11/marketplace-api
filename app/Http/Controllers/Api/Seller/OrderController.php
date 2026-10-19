<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // GET /api/seller/orders
    public function index(Request $request)
    {
        $store = Store::where('user_id', $request->user()->id)->firstOrFail();

        $orders = Order::whereHas('items.product', function ($q) use ($store) {
            $q->where('store_id', $store->id);
        })->with('items.product', 'user')->latest()->get();

        return response()->json($orders);
    }

    // PUT /api/seller/orders/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);

        $store = Store::where('user_id', $request->user()->id)->firstOrFail();

        $order = Order::whereHas('items.product', function ($q) use ($store) {
            $q->where('store_id', $store->id);
        })->findOrFail($id);

        $order->status = $request->status;
        $order->save();

        return response()->json($order);
    }
}