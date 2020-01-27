<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

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
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public static function getNearBy($lat, $lng, $distance, $distanceIn = 'km')
    {
        if ($distanceIn == 'km') {
            $results = self::select(['*', \DB::raw('( 0.621371 * 3959 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians(latitude) ) ) ) AS distance')])->havingRaw('distance < '.$distance)->get();
        } else {
            $results = self::select(['*', \DB::raw('( 3959 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians(latitude) ) ) ) AS distance')])->havingRaw('distance < '.$distance)->get();
        }
        return $results;
    }
}
