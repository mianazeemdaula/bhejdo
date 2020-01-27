<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function lifter()
    {
        return $this->belongsTo(User::class);
    }
}
