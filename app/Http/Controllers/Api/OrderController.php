<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // GET /api/orders
    public function index(Request $request)
    {
        $orders = Order::with('items.product', 'payment')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // GET /api/orders/{id}
    public function show(Request $request, $id)
    {
        $order = Order::with('items.product', 'payment')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($order);
    }

    // POST /api/orders  (checkout dari cart)
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
        ]);

        $cart = Cart::with('items.product')
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong'], 400);
        }

        $order = DB::transaction(function () use ($cart, $request) {
            $totalAmount = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => 'manual',
                'status' => 'pending',
                'amount' => $totalAmount,
            ]);

            // Kosongkan cart setelah checkout
            $cart->items()->delete();

            return $order;
        });

        return response()->json($order->load('items.product', 'payment'), 201);
    }
}