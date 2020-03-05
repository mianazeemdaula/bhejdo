<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCharge extends Model
{
    protected $fillable = [
        'user_id', 'type', 'description', 'amount', 'balance'
    ];



    // Customer Methods

    public static function balance($user)
    {
        return self::where('user_id', $user)->latest('id')->first();
    }

    public static function add($user, $description, $type, $amount)
    {
        $balance = self::balance($user);
        if($balance == null){
            return self::create([
                'user_id' => $user,
                'description' => $description,
                'type' => $type,
                'amount' => $amount,
                'balance' => $amount
            ]);
        }else{
            return self::create([
                'user_id' => $user,
                'description' => $description,
                'type' => $type,
                'amount' => $amount,
                'balance' => $balance->balance + $amount
            ]);
        }
    }

    public static function deduct($user, $description, $type, $amount)
    {
        $balance = self::balance($user);
        if($balance == null){
            return self::create([
                'user_id' => $user,
                'description' => $description,
                'type' => $type,
                'amount' => $amount,
                'balance' => $amount
            ]);
        }else{
            return self::create([
                'user_id' => $user,
                'description' => $description,
                'type' => $type,
                'amount' => -$amount,
                'balance' => $balance->balance - $amount
            ]);
        }
    }
}
