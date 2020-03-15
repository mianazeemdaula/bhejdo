<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\LifterLocation;
use App\Service;
use Carbon\Carbon;

class LifterController extends Controller
{
    public function getNearMe(Request $request)
    {
        $lifters = LifterLocation::where('location', 'nearSphere', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($request->lat), // longitude
                    floatval($request->lon), // latitude
                ],
            ],
            '$maxDistance' => intval($request->distance * 1000),
        ])->where('last_update', '>', Carbon::now()->subSeconds(21)->timestamp)
        ->where('services','all',[intval($request->service)])->get();
        $service = Service::find($request->service);
        $smaple = \App\Order::where('type',3)->where('consumer_id', $request->user()->id)->count();
        return ['status' => true, 'data' => ['lifters' => $lifters, 'service' => $service, 'sample' => $smaple]];
        //return $stats;
    }
}
