<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\LifterLocation;

class MilkLifterController extends Controller
{
    public function getMilkLifters(Request $request)
    {
        return User::getNearBy($request->lat, $request->lng, $request->distance, 'milk-lifter');
    }

    public function getNearMe(Request $request)
    {
        //return $request->all();
        // $params = [
        //     'index' => 'lifter_location',
        //     'body'  => [
        //         'query' => [
        //             'bool' => [
        //                 "filter"=> [
        //                     "geo_distance" => [
        //                         "distance" => "5km",
        //                         "pin.location" => [
        //                             "lat" => $request->lat,
        //                             "lon" => $request->lon
        //                         ]
        //                     ]
        //                 ]
                        
        //             ]
        //         ]
        //     ]
        // ];
        // $stats = \Elasticsearch::search($params);
        $lifters = lifterLocation::where('location', 'nearSphere', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($request->lat), // longitude
                    floatval($request->lon), // latitude
                ],
            ],
            '$maxDistance' => intval($request->distance * 1000),
        ])->get();
        return $lifters;
        //return $stats;
    }
}
