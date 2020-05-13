<?php

namespace App\Http\Resources\V2\Consumer;

use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date_of_signup' => $this->created_at,
            'recentShopping' => 250,
            'totalShopping' => 52500,
            'recentCommission' => 250,
            'totalCommission' => 6520,
            'expiry' => \Carbon\Carbon::parse($this->created_at)->addYears(1),
        ];
    }
}
