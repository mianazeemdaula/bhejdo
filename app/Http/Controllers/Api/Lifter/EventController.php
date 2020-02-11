<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Events\NewLocation;
use Elasticsearch\ClientBuilder;

class EventController extends Controller
{
    public function lifterLocation(Request $request)
    {
        $currentMilliSecond = (int) (microtime(true) * 1000);
        $data = [
            'body' => [
                "doc" => [
                    "pin" => [
                        'location' => [
                            'lat' => $request->lat,
                            'lon' => $request->lon,
                        ]
                    ],
                    'last_update' => $currentMilliSecond,
                    'lifter_id' => $request->user()->id
                ]
            ],
            'index' => 'lifter_location',
            'id' => 'lifter_'.$request->user()->id,
        ];
        $return = \Elasticsearch::index($data);
        event(new NewLocation($request->user()->id, $request->lat, $request->lon));
        return $return;
    }

    public function getAll()
    {
        $data = [
            'index' => 'lifter_location',
            'body'  => [
                "query" => [
                    "match_all" => (Object)[]
                ]
            ]
        ];
        $stats = \Elasticsearch::search($data);
        return $return;
    }

    public function getLocation($lat, $lon)
    {
        $params = [
            'index' => 'lifter_location',
            'body'  => [
                'query' => [
                    'bool' => [
                        "filter"=> [
                            "geo_distance" => [
                                "distance" => "25km",
                                "pin.location" => [
                                    "lat" => $lat,
                                    "lon" => $lon
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

    public function getStatus()
    {
        
        $stats = \Elasticsearch::cluster()->stats();
        return $stats;
    }
}
