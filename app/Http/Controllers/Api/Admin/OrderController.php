<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(
            Order::with('items.product', 'user')->latest()->get()
        );
    }
}