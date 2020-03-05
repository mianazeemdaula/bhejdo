<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function delivery()
    {
        return $this->hasMany(Delivery::class);
    }

    public function consumer()
    {
        return $this->belongsTo(User::class);
    }

    public function lifter()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function review()
    {
        return $this->hasOne('App\Review');
    }
}
