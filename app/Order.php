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

    private function review()
    {
        return $this->hasOne('App\Review');
    }

    public function consumerReview()
    {
        return $this->review()->where('type','consumer');
    }

    public function lifterReview()
    {
        return $this->review()->where('type','lifter');
    }
}
