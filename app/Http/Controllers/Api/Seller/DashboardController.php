<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $store = Store::where('user_id', $request->user()->id)->first();

        if (!$store) {
            return response()->json(['message' => 'Toko belum dibuat'], 404);
        }

        $totalProducts = $store->products()->count();
        $totalStock = $store->products()->sum('stock');

        return response()->json([
            'store' => $store,
            'total_products' => $totalProducts,
            'total_stock' => $totalStock,
        ]);
    }

    public function createStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $existing = Store::where('user_id', $request->user()->id)->first();
        if ($existing) {
            return response()->json(['message' => 'Toko sudah ada'], 400);
        }

        $store = Store::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'address' => $request->address,
        ]);

        return response()->json($store, 201);
    }

    public function updateStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'logo' => 'nullable|string',
        ]);

        $store = Store::where('user_id', $request->user()->id)->firstOrFail();

        $store->update($request->only(['name', 'description', 'address', 'logo']));

        return response()->json($store);
    }
}