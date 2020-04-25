<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartOrderDetail extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'price', 'qty'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
