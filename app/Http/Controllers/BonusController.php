<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Bonus;

class BonusController extends Controller
{
    public function index(User $user)
    {
        $collection = $user->bonus()->latest()->get();
        return view('pages.admin.charges.index', compact('collection'));
    }
}
