<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $fillable = [
        'user_id', 'description', 'amount', 'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
