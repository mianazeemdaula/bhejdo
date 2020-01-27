<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;

class OrderController extends Controller
{
    public function index()
    {
        $collection = Order::al();
        return view('admin.order.index');
    }
}
