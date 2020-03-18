<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;

class OrderController extends Controller
{
    public function index()
    {
        $collection = Order::latest('id')->limit(200)->get();
        return view('pages.admin.order.index', compact('collection'));
    }
}
