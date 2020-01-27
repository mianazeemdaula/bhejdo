<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Helpers\AndroidNotifications;
class LifterController extends Controller
{
    
    public function FunctionName(Request $request)
    {
        $haversine = "SQRT(POW((69.1 * (latitude - $request->latitude)), 2 ) +
        POW((53 * (longitude - $request->longitude)), 2))";
        $rows =  User::select(['*']) //pick the columns you want here.
            ->selectRaw("{$haversine} AS distance")
            ->whereRaw("{$haversine} < ?", [$request->radius]);
        return $rows;
    }

    public function lifters(Request $request)
    {
        return User::getNearBy($request->latitude, $request->longitude, $request->radius);
    }

    public function notification(Request $request)
    {
        return AndroidNotifications::to($request->title, $request->message, $request->token,[]);
    }
}
