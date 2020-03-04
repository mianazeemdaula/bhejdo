<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id', 'description', 'amount', 'type', 'balance'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
