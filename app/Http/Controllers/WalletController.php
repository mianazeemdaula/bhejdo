<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('pages.admin.services.index', compact('services'));
    }
}
