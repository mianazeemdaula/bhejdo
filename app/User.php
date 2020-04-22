<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pushToken'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function services()
    {
        return $this->belongsToMany('App\Service')->using('App\ServiceUser')->withPivot([
            'service_id',
            'user_id',
            'status',
            'level_id'
        ]);
    }

    public function city()
    {
        return $this->belongsTo('App\City');
    }

    
    public function storLifter()
    {
        return $this->belongsToMany('App\User')->using('App\StoreLifter')->withPivot([
            'store_id',
            'lifter_id'
        ]);
    }

    public function wallet()
    {
        return $this->hasMany(Wallet::class);
    }

    public function bonus()
    {
        return $this->hasMany(Bonus::class);
    }

    public function orders()
    {
        return $this->hasMany('App\Order','lifter_id','id');
    }

    public function serviceCharges()
    {
        return $this->hasMany(ServiceCharge::class);
    }

    public function reviews()
    {
        return $this->hasMany('App\Review');
    }

    public function scopeIsWithinMaxDistance($query, $location, $radius = 5) {

        $haversine = "(6371 * acos(cos(radians($location->latitude)) 
                        * cos(radians(model.latitude)) 
                        * cos(radians(model.longitude) 
                        - radians($location->longitude)) 
                        + sin(radians($location->latitude)) 
                        * sin(radians(model.latitude))))";
        return $query
           ->select() //pick the columns you want here.
           ->selectRaw("{$haversine} AS distance")
           ->whereRaw("{$haversine} < ?", [$radius]);
   }

    public static function getNearBy($lat, $lng, $distance, $where, $distanceIn = 'km')
    {
        if ($distanceIn == 'km') {
            $results = self::select(['*', \DB::raw('( 0.621371 * 3959 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians(latitude) ) ) ) AS distance')])->havingRaw('distance < '.$distance)->role($where)->get();
        } else {
            $results = self::select(['*', \DB::raw('( 3959 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians(latitude) ) ) ) AS distance')])->havingRaw('distance < '.$distance)->role($where)->get();
        }
        return $results;
    }
}
