<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function consumer()
    {
        return $this->hasMany(User::class,'consumer_id');
    }
}
