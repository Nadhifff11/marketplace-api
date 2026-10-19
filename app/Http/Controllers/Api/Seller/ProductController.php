<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function getStore(Request $request)
    {
        return Store::where('user_id', $request->user()->id)->firstOrFail();
    }

    public function index(Request $request)
    {
        $store = $this->getStore($request);
        $products = Product::where('store_id', $store->id)->latest()->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $store = $this->getStore($request);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $store = $this->getStore($request);
        $product = Product::where('store_id', $store->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'category_id', 'description', 'price', 'stock']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json($product);
    }

    public function destroy(Request $request, $id)
    {
        $store = $this->getStore($request);
        $product = Product::where('store_id', $store->id)->findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Produk dihapus']);
    }
}