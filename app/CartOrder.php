<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartOrder extends Model
{
    public function consumer()
    {
        return $this->belongsTo('App\User','consumer_id','id');
    }

    public function lifter()
    {
        return $this->belongsTo('App\User','lifter_id','id');
    }

    public function store()
    {
        return $this->belongsTo('App\User','store_id','id');
    }

    public function address()
    {
        return $this->hasOne(Address::class,'id','address_id');
    }

    public function details()
    {
        return $this->hasMany('App\CartOrderDetail','order_id','id');
    }
}
