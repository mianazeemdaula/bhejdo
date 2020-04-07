<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleOrder extends Model
{
    //

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    protected $casts = [
        'days' => 'array',
    ];
}
