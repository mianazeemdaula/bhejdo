<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Order;
use App\OpenOrder;
use App\Helpers\AndroidNotifications;
use App\User;
use App\Http\Resources\Order\Order as OrderResource;
use App\LifterLocation;

class OrderProcess {

    static public function orderCreated(Order $order)
    {
        try{
            $lifters = self::getNearMe($order->latitude, $order->longitude, 3, $order->service_id);
            $lCount = count($lifters);
            $tokens = Array();
            $users = Array();
            foreach ($lifters as $lifter) {
                $lifterid = $lifter['lifter_id'];
                $_lifter = User::find($lifterid);
                if($_lifter != null && $_lifter->pushToken != null && $_lifter->type != 'consumer'){
                    $users[] = $_lifter->id;
                    $tokens[] = $_lifter->pushToken;
                }
            }
            $message = "Place order of $order->qty liter of ".$order->service->s_name.". Please deliver as earliest.";
            // Send Notification to Lifter
            $args =  ["type" => 'new_order', 'order_id' => $order->id , 'order' => new OrderResource($order)];
            $notification = AndroidNotifications::MultipleLifter("New Order", $message, $tokens, $args);
            return ['count' => $lCount, 'notification' => $notification, 'order' => $order->id,'users' => $users];
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
        $lifters = LifterLocation::where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($lat), // longitude
                    floatval($lon), // latitude
                ],
            ],
            '$maxDistance' => intval(3 * 1000)
        ])
        ->where("onwork","1")
        ->where('services','all',[$service])->get();
        //->where('last_update', '>', Carbon::now()->subSeconds(120)->timestamp)
        return $lifters;
    }
}