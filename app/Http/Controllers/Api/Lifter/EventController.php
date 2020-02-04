<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Events\NewLocation;

class EventController extends Controller
{
    public function lifterLocation(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        event(new NewLocation($user));
        return "Pushed";
    }
}
