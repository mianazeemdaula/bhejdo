<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LifterController extends Controller
{
    public function getNearMe(Request $request)
    {
        $lifters = lifterLocation::where('location', 'nearSphere', [
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
        $services = Service::all();
        return ['status' => true, 'data' => ['lifters' => $lifters, 'service' => $services]];
        //return $stats;
    }
}
