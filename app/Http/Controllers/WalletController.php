<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bonus;
use App\Wallet;
use App\User;

class WalletController extends Controller
{
    public function index(User $user)
    {
        $user = $user;
        $collection = $user->wallet()->latest()->get();
        return view('pages.admin.wallet.index', compact('collection', 'user'));
    }
}
