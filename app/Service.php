<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function users()
    {
        // WOrking before new pivot
        return $this->belongsToMany('App\User')->using('App\ServiceUser')->withPivot([
            'service_id',
            'user_id',
            'level_id',
            'status',
        ]);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scheduleorder()
    {
        return $this->hasMany(ScheduleOrder::class);
    }

    public function levels()
    {
        return $this->hasMany(Level::class);
    }

    public function userlevels()
    {
        return $this->hasMany(Level::class);
    }
}
