<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class PartnerLocation extends Model
{
    use SpatialTrait;

    protected $casts = [
        'services' => 'array',
    ];

    protected $spatialFields = [
        'location',
    ];
}
