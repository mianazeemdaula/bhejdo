<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'title', 'description', 'promo_code', 'amount', 'type', 'category', 'expiry_date', 'status', 'shopping_limit', 'credit', 'statement'
    ];
}
