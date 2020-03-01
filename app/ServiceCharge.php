<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCharge extends Model
{
    protected $fillable = [
        'user_id', 'type', 'description', 'amount'
    ];
}
