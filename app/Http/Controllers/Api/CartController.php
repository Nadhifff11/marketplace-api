<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Ambil atau buat cart milik user yang login
    private function getOrCreateCart($userId)
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    // GET /api/cart
    public function index(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->id);
        $cart->load('items.product');

        return response()->json($cart);
    }

    // POST /api/cart
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($request->user()->id);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json($item->load('product'), 201);
    }

    // PUT /api/cart/{id}
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::whereHas('cart', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $item->quantity = $request->quantity;
        $item->save();

        return response()->json($item->load('product'));
    }

    // DELETE /api/cart/{id}
    public function destroy(Request $request, $id)
    {
        $item = CartItem::whereHas('cart', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $item->delete();

        return response()->json(['message' => 'Item dihapus dari keranjang']);
    }
}