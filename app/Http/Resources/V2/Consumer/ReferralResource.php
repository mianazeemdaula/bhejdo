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
        $recent = \App\CartOrder::where('consumer_id',$this->id)->where('status','droped')->latest()->first();
        $payable = \App\CartOrder::where('consumer_id', $this->id)->where('status','droped')->select(\DB::raw("SUM(payable_amount-charges-consumer_bonus) as amount"))->groupBy('consumer_id')->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dateOfSignup' => $this->created_at,
            'recentShopping' => $recent == null ? 0 : $recent->payable_amount - $recent->charges - $recent->consumer_bonus,
            'totalShopping' => $payable == null ? 0 : round($payable->amount),
            'recentCommission' => $recent == null ? 0 : round((($recent->payable_amount - $recent->charges - $recent->consumer_bonus) * 2 ) / 100),
            'totalCommission' => $payable == null ? 0 : round(($payable->amount * 2) / 100),
            'expiry' => \Carbon\Carbon::parse($this->created_at)->addYears(1),
        ];
    }
}
