<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with('product')
            ->whereHas('customer', fn ($query) => $query->where('user_id', Auth::id()))
            ->latest()
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }
}
