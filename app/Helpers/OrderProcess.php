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
            $key = 'order_queue_notificaton_'.$order->id;
            $partners = self::getNearMe($order->latitude, $order->longitude, 3, $order->service_id);
            $livePartners = [];
            foreach($partners as $partner){
                $latlong = $partner->location['coordinates'];
                $livePartners[] = ['id' => $partner->lifter_id, 'distance' => self::distance($latlong[0], $latlong[1],$order->latitude, $order->longitude,"K"), 'location' => $latlong ];
            }
            usort($livePartners, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
            // Caculated distance and lifters
            $queue = [];
            $queueFail = [];
            
            if(\PRedis::exists($key)){
                $queue = json_decode(\PRedis::get($key));
                $queueFail = json_decode(\PRedis::get($key."_fail"));
            }

            foreach($livePartners as $partner){
                $lifterid = $partner['id'];
                if(!in_array($lifterid, $queue)){
                    // Do processing for notificaton
                    // if partner not cancel the acceptance
                    if(!Cache::has('order_notificaton_'.$lifterid."_".$order->id)){
                        // if another notifation in last 100 seconds
                        if(!Cache::has('neworder_time_'.$lifterid)){
                            Cache::put('neworder_time_'.$lifterid, true, 100);
                            $user = User::find($lifterid);
                            $message = "Place order of $order->qty liter of ".$order->service->s_name.". Please deliver as earliest.";
                            // Send Notification to Lifter
                 
                            $args =  ["type" => 'new_order', 'order_id' => $order->id , 'order' => new OrderResource($order)];
                            $notification = AndroidNotifications::toLifter("New Order", $message, $user->pushToken, $args);
                            $respone = json_decode($notification);
                            if($respone->success){
                                $queue[] = $lifterid;
                                break;
                            }else{
                                $queueFail[] = $lifterid;
                            }
                        }
                    }
                }
            }
            \PRedis::set($key, json_encode($queue), 90000); // 25 hours
            \PRedis::set($key."_fail", json_encode($queueFail), 90000); // 25 hours
            return ['sucess'=> $queue, 'fail' => $queueFail, 'order' => $order->id];
        }catch(Exception $ex){
            return $ex;
        }
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