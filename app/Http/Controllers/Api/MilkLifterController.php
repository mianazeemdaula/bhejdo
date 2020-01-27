<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
class MilkLifterController extends Controller
{
    public function getMilkLifters(Request $request)
    {
        return User::role('milk-lifter')->getNearBy($request->lat, $request->lng, $request->distance)->get();
    }
}
