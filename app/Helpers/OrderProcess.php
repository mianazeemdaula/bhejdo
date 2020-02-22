<?php

namespace App\Helpers;

use App\OpenOrder;
use App\Helpers\AndroidNotifications;
use App\User;

class OrderProcess {

    static public function newOrder(OpenOrder $order)
    {
        try{
            $nears = self::getNearMe($order->latitude, $order->longitude);
            $lCount = count($nears);
            $noti = [];
            foreach ($lifters as $lifter) {
                $lifterid = $data['lifter_id'];
                $_lifter = User::findOrFail($lifterid); 
                $message = "Place order of $order->qty liter of milk. Please deliver as earlist.";
                // Send Notification to Lifter
                $args =  ["type" => 'new_order', 'order_id' => $order->id ];
                $notification = AndroidNotifications::toLifter("New Milk Order", $message, $_lifter->pushToken,$args);
                $noti[] = $notification;
            }
            return ['count' => $lCount, 'noti' => $noti, 'lat' => $order->latitude, 'lon' => $order->longitude];
        }catch(Exception $ex){
            return $ex;
        }
        return true;
    }

    static public function getNearMeElastic($lat, $lon)
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
                ]
            ]
        ];
        $stats = \Elasticsearch::search($params);
        return $stats;
    }

    static public function getNearMe($lat, $lon)
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
        ])->get();
        return $lifters;
    }
}