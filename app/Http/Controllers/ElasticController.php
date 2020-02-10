<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ElasticController extends Controller
{
    public function mapLocation()
    {
        $params = [
            'index' => 'lifter_location',
            'body' => [
                'mappings' => [
                    "properties" => [
                        "location" => [
                            "type" => "geo_point"
                        ],
                        "lifter_id" =>[
                            "type" => "integer"
                        ],
                        "lifter_orders" =>[
                            "type" => "integer"
                        ],
                        "star_rating" =>[
                            "type" => "half_float"
                        ],
                        "name" => [
                            "type" => "text"
                        ],
                        "avatar" => [
                            "type" => "text"
                        ],
                        "account_type" => [
                            "type" => "text"
                        ],
                        "last_update" => [
                            "type" => "date_nanos"
                        ],
                    ]
                ]
            ]
        ];
        $response = \Elasticsearch::indices()->create($params);
    }

    public function clearIndices()
    {
        $response = \Elasticsearch::indices()->delete(['index' => 'lifter_location']);
    }
}
