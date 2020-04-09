<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Order;
use App\Helpers\AndroidNotifications;
use App\User;
use App\Http\Resources\Order\Order as OrderResource;
use App\LifterLocation;
use Illuminate\Support\Facades\Cache;

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
                if(!Cache::has('order_notificaton_'.$lifterid."_".$order->id)){
                    if(!Cache::has('neworder_time_'.$lifterid)){
                        Cache::put('neworder_time_'.$lifterid, true, 100);
                        $_lifter = User::find($lifterid);
                        if($_lifter != null && $_lifter->pushToken != null && $_lifter->type != 'consumer'){
                            $users[] = $_lifter->id;
                            $tokens[] = $_lifter->pushToken;
                        }
                    }
                }
            }
            if(count($tokens) > 0){
                $message = "Place order of $order->qty liter of ".$order->service->s_name.". Please deliver as earliest.";
                // Send Notification to Lifter
                $args =  ["type" => 'new_order', 'order_id' => $order->id , 'order' => new OrderResource($order)];
                $notification2 = AndroidNotifications::MultiplePartner("New Order", $message, $tokens, $args);
                return ['count' => $lCount, 'partners' => $notification2 ,'order' => $order->id,'users' => $users];
            }
        }catch(Exception $ex){
            return $ex;
        }
        return true;
    }

    static public function orderAssign(Order $order)
    {
        try{
            $key = 'partner_notificaton_'.$order->id;
            $lifters = self::getNearMe($order->latitude, $order->longitude, 3, $order->service_id);
            $data = [];
            foreach($lifters as $lifter){
                $latlong = $lifter->location['coordinates'];
                $data[] = ['id' => $lifter->lifter_id, 'distance' => self::distance($latlong[0], $latlong[1],$order->latitude, $order->longitude,"K") ];
            }
            $redis = \PRedis::command('GEORADIUS',['partner_locations' ,$order->latitude, $order->longitude, 3, 'km', ['WITHDIST','WITHCOORD', 20, 'ASC']]);
            if(Cache::has($key)){
                $data = Cache::get($key);
            }
            return [ 'mongo' => $data, 'redis' => $redis];
            $myArray = [[
                'id' => 25,
                'distance' => 0.900,
                'pushToken' => "lasdjlfsjfljslfjslfjlaskjfs;kldjf",
                'notification' => true,
                'cancel' => true,
                'time' => "2656565565653232",
            ], [
                'id' => 24,
                'distance' => 0.5826,
                'pushToken' => "lasdjlfsjfljslfjslfjlaskjfs;kldjf",
                'notification' => true,
                'cancel' => true,
                'time' => "2656565565653232",
            ],
            [
                'id' => 365,
                'distance' => 0.200,
                'pushToken' => "lasdjlfsjfljslfjslfjlaskjfs;kldjf",
                'notification' => true,
                'cancel' => true,
                'time' => "2656565565653232",
            ],
            [
                'id' => 145,
                'distance' => 0.999958,
                'pushToken' => "lasdjlfsjfljslfjslfjlaskjfs;kldjf",
                'notification' => true,
                'cancel' => true,
                'time' => "2656565565653232",
            ]];

            usort($myArray, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
            return $myArray;
            // $lifters = self::getNearMe($order->latitude, $order->longitude, 3, $order->service_id);
            // $lCount = count($lifters);
            // $tokens = Array();
            // $users = Array();
            // foreach ($lifters as $lifter) {
            //     $lifterid = $lifter['lifter_id'];
            //     if(!Cache::has('order_notificaton_'.$lifterid."_".$order->id)){
            //         if(!Cache::has('neworder_time_'.$lifterid)){
            //             Cache::put('neworder_time_'.$lifterid, true, 100);
            //             $_lifter = User::find($lifterid);
            //             if($_lifter != null && $_lifter->pushToken != null && $_lifter->type != 'consumer'){
            //                 $users[] = $_lifter->id;
            //                 $tokens[] = $_lifter->pushToken;
            //             }
            //         }
            //     }
            // }
            // if(count($tokens) > 0){
            //     $message = "Place order of $order->qty liter of ".$order->service->s_name.". Please deliver as earliest.";
            //     // Send Notification to Lifter
            //     $args =  ["type" => 'new_order', 'order_id' => $order->id , 'order' => new OrderResource($order)];
            //     $notification = AndroidNotifications::MultipleLifter("New Order", $message, $tokens, $args);
            //     $notification2 = AndroidNotifications::MultiplePartner("New Order", $message, $tokens, $args);
            //     return ['count' => $lCount, 'notification' => $notification, 'partners' => $notification2 ,'order' => $order->id,'users' => $users];
            // }
        }catch(Exception $ex){
            return $ex;
        }
        return true;
    }

    static public function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);
        
            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
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