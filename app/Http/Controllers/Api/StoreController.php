<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;

class StoreController extends Controller
{
    // GET /api/stores/{slug}
    public function show($slug)
    {
        $store = Store::with('products')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($store);
    }
}