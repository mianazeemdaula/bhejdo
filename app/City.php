<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name','open_time','close_time','status'];
    
    public function user()
    {
        return $this->hasMany(User::class);
    }
}
