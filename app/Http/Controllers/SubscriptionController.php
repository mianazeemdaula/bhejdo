<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $collection = Subscription::latest('id')->get();
        return view('pages.admin.subscription.index', compact('collection'));
    }
}
