<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartOrder extends Model
{
    public function consumer()
    {
        return $this->belongsTo(User::class);
    }

    public function lifter()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany('App\CartOrderDetail','order_id','id');
    }
}
