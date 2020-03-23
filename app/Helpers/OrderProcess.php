<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Order;
use App\OpenOrder;
use App\Helpers\AndroidNotifications;
use App\User;
use App\LifterLocation;

class OrderProcess {

    static public function orderCreated(Order $order)
    {
        try{
            $lifters = self::getNearMe($order->latitude, $order->longitude, 5, $order->service_id);
            $lCount = count($lifters);
            $noti = [];
            foreach ($lifters as $lifter) {
                $lifterid = $lifter['lifter_id'];
                $_lifter = User::findOrFail($lifterid); 
                $message = "Place order of $order->qty liter of ".$order->service->s_name.". Please deliver as earlist.";
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

    static public function getNearMe($lat, $lon, $distance, $service)
    {
        $lifters = lifterLocation::where('location', 'nearSphere', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($lat), // longitude
                    floatval($lon), // latitude
                ],
            ],
            '$maxDistance' => intval($distance * 1000)
        ])
        ->where('services','all',[$service])->get();
        //->where('last_update', '>', Carbon::now()->subSeconds(120)->timestamp)
        return $lifters;
    }
}