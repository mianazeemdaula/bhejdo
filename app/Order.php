<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'lifter_id', 'status',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function consumer()
    {
        return $this->belongsTo(User::class);
    }
}
