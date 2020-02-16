<?php

namespace App\Helpers;
use App\User;
use App\Bonus;

class BonusProcess {

    public function lifterRegBonus(User $reffer, $mobile)
    {
        try{
            $ammount = 0;
            if($reffer->hasRole('store')){
                $ammount = 50;
            }else if($reffer->hasRole('lifter')){
                $ammount = 50;
            }else{
                return false;
            }
            $bonus = new Bonus();
            $bonus->user_id = $reffer->id;
            $bonus->description = "Registration bonus of $mobile";
            $bonus->debit = 50;
            $bonus->balance = 50;
            $bonus->save();
            return true;
        }catch(Exception $ex){
            return $ex;
        }
        return true;
    }
}