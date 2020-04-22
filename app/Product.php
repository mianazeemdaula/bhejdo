<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function city()
    {
        return $this->belongsTo('App\City','city_id','id');
    }

    public function company()
    {
        return $this->belongsTo('App\User','company_id','id');
    }
}
