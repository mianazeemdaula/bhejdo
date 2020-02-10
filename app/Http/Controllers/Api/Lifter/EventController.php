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
                'location' => [
                    'lat' => $request->lat,
                    'lon' => $request->lon,
                ],
                'last_update' => $currentMilliSecond,
                'lifter_id' => $request->user()->id
            ],
            'index' => 'lifter_location',
            'id' => 'lifter_'.$request->user()->id,
        ];
        $return = \Elasticsearch::index($data);
        event(new NewLocation($request->user()->id, $request->lat, $request->lon));
        return $return;
    }

    public function locationIndex()
    {
        $data = [
            'body' => [
                'testField' => 'this is the test api'
            ],
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
        ];
        $stats = \Elasticsearch::indices()->stats(['index' => 'my_index']);
        return $stats;
        $return = \Elasticsearch::index($data);
        return $return;
    }

    public function getLocation($lat, $lon)
    {
        $params = [
            'index' => 'lifter_location',
            'body'  => [
                "query" => [
                    "bool" => [
                        "must" => [
                            "match_all" => []
                        ],
                        "filtered" => [
                            "filter"=> [
                                "geo_distance" => [
                                    "distance" => "5km",
                                    "location.location" => [
                                        "lat" => 28.5402707,
                                        "lon" => 77.2289643
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $params = [
            'index' => 'lifter_location',
            'body'  => [
                'query' => [
                    "filtered" => [
                        "filter"=> [
                            "geo_distance" => [
                                "distance" => "5km",
                                "location.location" => [
                                    "lat" => 28.5402707,
                                    "lon" => 77.2289643
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
