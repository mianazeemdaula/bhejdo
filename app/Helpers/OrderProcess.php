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
            $lifters = $nears['hits']['hits'];
            foreach ($lifters as $lifter) {
                $data = $lifter['_source'];
                $lifterid = $data['lifter_id'];
                $_lifter = User::findOrFail($lifterid); 
                $message = "Place order of $order->qty liter of milk. Please deliver as earlist.";
                // Send Notification to Lifter
                $args =  ["type" => 'new_order', 'order_id' => $order->id ];
                $notification = AndroidNotifications::toLifter("New Milk Order", $message, $_lifter->pushToken,$args);
            }
        }catch(Exception $ex){
            return $ex;
        }
        return true;
    }

    static public function getNearMe($lat, $lon)
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
        return json_decode($stats);
    }

    static public function toLifter($title, $messag, $token,Array $data)
    {
        $pushKey = 'AIzaSyCvnrtqXFcB7ZfYfVT-kuugsEKvmHP7iok';
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $title,
            'body' => $messag,
            'badge' => 1, 
            'sound' => 'default'
        ];
        $fcmNotification = [
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => array_merge([ 'click_action' => 'FLUTTER_NOTIFICATION_CLICK'], $data)
        ];
        $headers = [
            'Authorization: key='. $pushKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    static public function MultipleConsumer($tokenList, $messag, $token, Array $data)
    {
        $pushKey = 'AIzaSyDt9hhxcjoDSd6Tf5KHz7CkMTh8hTsECVo';
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $title,
            'body' => $messag,
            'badge' => 1, 
            'sound' => 'default'
        ];
        
        $fcmNotification = [
            'registration_ids' => $tokenList, //multple token array
            'notification' => $notification,
            'data' => $data
        ];

        $headers = [
            'Authorization: key='. $pushKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    static public function MultipleLifter($tokenList, $messag, $token, Array $data)
    {
        $pushKey = 'AIzaSyCvnrtqXFcB7ZfYfVT-kuugsEKvmHP7iok';
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $title,
            'body' => $messag,
            'badge' => 1, 
            'sound' => 'default'
        ];
        
        $fcmNotification = [
            'registration_ids' => $tokenList, //multple token array
            'notification' => $notification,
            'data' => $data
        ];

        $headers = [
            'Authorization: key='. $pushKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}