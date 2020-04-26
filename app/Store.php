<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Store extends Model
{
    use SpatialTrait;
    protected $fillable = ['location', 'radius'];
    protected $spatialFields = [
        'location',
    ];
    
    public function user()
    {
        return $this->morphOne('App\User', 'profileable');
    }
}
