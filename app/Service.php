<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function users()
    {
        return $this->belongsToMany('App\User')->using('App\ServiceUser')->withPivot([
            'service_id',
            'user_id',
        ]);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
