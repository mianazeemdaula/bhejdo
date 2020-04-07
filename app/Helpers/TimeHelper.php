<?php
namespace App\Helpers;

class TimeHelper{

    static public function parseTime($deliveryTime)
    {
        if(strpos($deliveryTime, "PM") !== false){
            if(strlen($deliveryTime) == 7){
                $hours = (int) substr($deliveryTime,0,2);
                $minute = (int) substr($deliveryTime,3,5);
                $hours += 12;
                $deliveryTime = "$hours:$minute:00";
            }elseif(strlen($deliveryTime) == 4){
                $hours = (int) substr($deliveryTime,0,2);
                $hours += 12;
                $deliveryTime = "$hours:00:00";
            }
        }else if(strpos($deliveryTime, "AM") !== false){
            if(strlen($deliveryTime) == 7){
                $hours = (int) substr($deliveryTime,0,2);
                $minute = (int) substr($deliveryTime,3,5);
                $deliveryTime = "$hours:$minute:00";
            }elseif(strlen($deliveryTime) == 4){
                $hours = (int) substr($deliveryTime,0,2);
                $deliveryTime = "$hours:00:00";
            }
        }
        return $deliveryTime;
    }
}