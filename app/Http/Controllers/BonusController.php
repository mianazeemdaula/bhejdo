<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bonus;

class BonusController extends Controller
{
    public function index()
    {
        $bonuses = Bonus::all();
        return view('pages.super-admin.bonus.index', compact('bonuses'));
    }
}
