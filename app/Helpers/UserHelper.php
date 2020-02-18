<?php
namespace App\Helpers;

use Illuminate\Support\Str;
use App\User;


class UserHelper{
    static public function gerateId($username){
        $refferid = Str::limit($username,5);
        $newid = $newid.Str::random(10 - strlen($refferid));
        $isAvaialbe = User::where('reffer_id', $newid)->first();
        if($isAvaialbe == null){
            return $newid;
        }
        return self::gerateId($username);
    }
}