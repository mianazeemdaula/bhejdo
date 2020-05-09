<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function cityProducts($city)
    {
        return $this->hasMany('App\Product')->where('city_id',$city);
    }
}
