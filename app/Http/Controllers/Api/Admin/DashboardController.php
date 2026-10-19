<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_stores' => Store::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
        ]);
    }
}