<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Address extends Model
{
    use SpatialTrait;

    protected $fillable = [
        'title','address'
    ];

    protected $spatialFields = [
        'location',
    ];
}
