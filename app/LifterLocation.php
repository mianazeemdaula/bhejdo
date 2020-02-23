<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class LifterLocation extends Model
{
    //protected $collection = 'lifter_location';
    protected $connection = 'mongodb';
    protected $primaryKey = 'id';
    protected $fillable = [
        'lifter_id', 'orders', 'rating', 'name', 'avatar', 'account_type', 'services', 'last_update'
    ];
    public $timestamps = false;
}
