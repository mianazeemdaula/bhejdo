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
        $currentMilliSecond = (int) (microtime(true) * 100);
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
        $return = \Elasticsearch::update($data);
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
        return $stats;
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
                                "distance" => "5km",
                                "pin.location" => [
                                    "lat" => $lat,
                                    "lon" => $lon
                                ]
                            ]
                        ]   
                    ]
                ],
                "sort" =>  [
                    "_geo_distance" => [
                        "pin.location" => [
                            "lat" => $lat,
                            "lon" => $lon
                        ],
                        "order" => "asc",
                        "unit" => "km"
                    ],
                    "lifter_orders" => [
                        "order" => "desc",
                    ],
                    "star_rating" => [
                        "order" => "desc",
                    ],
                ],
                //"size" => 1

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
