<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenOrder extends Model
{
    public function consumer()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
