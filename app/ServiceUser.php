<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceUser extends Pivot
{
    //
    public function level()
    {
        return $this->hasOne(Level::class,'id','level_id');
    }
}
