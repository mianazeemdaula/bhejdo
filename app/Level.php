<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = [
        'service_id', 'l_name', 'order_qty',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function userService()
    {
        return $this->belongsTo(ServiceUser::class);
    }
}
