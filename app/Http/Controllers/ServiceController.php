<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('pages.admin.services.index', compact('services'));
    }

    public function setservice()
    {
        $user = User::find(1);
        $user->services()->sync([1,3]);
        $user->storelifter()->sync([2,3]);
        $service = Service::find(1);
        return $user->storelifter;
    }
}
