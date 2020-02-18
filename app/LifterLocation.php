<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class LifterLocation extends Model
{
    protected $collection = 'lifter_location';
    protected $connection = 'mongodb';
    //protected $primaryKey = 'id';
    protected $fillable = [
        'address',
    ];
}
