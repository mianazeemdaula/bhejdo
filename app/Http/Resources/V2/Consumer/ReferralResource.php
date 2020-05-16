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
        $recent = \App\CartOrder::where('consumer_id',$this->id)->latest()->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dateOfSignup' => $this->created_at,
            'recentShopping' => $recent == null ? 0 : $recent->payable_amount,
            'totalShopping' => 0,
            'recentCommission' => $recent == null ? 0 : (($recent->payable_amount - $recent->charges - $recent->consumer_bonus) * 2 ) / 100,
            'totalCommission' => \App\CartOrder::where('consumer_id', $this->id)->where('status','droped')->sum('payable_amount'),
            'expiry' => \Carbon\Carbon::parse($this->created_at)->addYears(1),
        ];
    }
}
