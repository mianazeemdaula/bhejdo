<?php

namespace App\Helpers;

class AndroidNotifications {
    static public function toConsumer($title, $messag, $token,Array $data)
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

    static public function toLifter($title, $messag, $token,Array $data)
    {
        $pushKey = 'AIzaSyCTO-6wlZ0iVLzGgrnGUCecizBoEYjgJ68';
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
        $pushKey = 'AIzaSyCTO-6wlZ0iVLzGgrnGUCecizBoEYjgJ68';
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