<?php
namespace App\Helpers;

use Illuminate\Support\Str;

class SmsHelper{
    static public function send($mobile, $message){
        $post = "sender=".urlencode("OhYes")."&mobile=".urlencode($mobile)."&message=".urlencode($message)."";
        $url = "https://sendpk.com/api/sms.php?username=923004103160&password=8m8a2r4w";
        $ch = curl_init();
        $timeout = 30; // set to zero for no timeout
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $result = curl_exec($ch); 
        return $result;
    }
}