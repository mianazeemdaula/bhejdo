<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public function order()
    {
        return $this->belongsTo(CartOrder::class, 'order_id', 'id');
    }
}
