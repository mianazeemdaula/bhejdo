<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenOrder extends Model
{
    public function consumer()
    {
        return $this->belongsTo(User::class);
    }
}
