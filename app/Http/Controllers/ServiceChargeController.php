<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ServiceCharge;
use App\User;


class ServiceChargeController extends Controller
{
    public function index(User $user)
    {
        $collection = $user->serviceCharges()->latest()->get();
        return view('pages.admin.charges.index', compact('collection'));
    }
}
