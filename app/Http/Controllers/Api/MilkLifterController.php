<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
class MilkLifterController extends Controller
{
    public function getMilkLifters(Request $request)
    {
        return User::getNearBy($request->lat, $request->lng, $request->distance, 'milk-lifter');
    }

    public function getNearMe(Request $request)
    {
        $params = [
            'index' => 'lifter_location',
            'body'  => [
                'query' => [
                    'bool' => [
                        "filter"=> [
                            "geo_distance" => [
                                "distance" => "5km",
                                "pin.location" => [
                                    "lat" => $request->lat,
                                    "lon" => $request->lon
                                ]
                            ]
                        ]
                        
                    ]
                ]
            ]
        ];
        $stats = \Elasticsearch::search($params);
        return $stats;
    }
}
