<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumerProfile extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $rating = \App\Order::leftJoin('reviews','reviews.order_id','=','orders.id')->where('orders.consumer_id',$this->id)->where('reviews.type','consumer')->avg('reviews.starts');
        $wallet = $this->wallet()->orderBy('id','desc')->first();
        $bonus = $this->bonus()->orderBy('id','desc')->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar == null ? null : asset("storage/".$this->avatar),
            'rating' => $rating == null ? (double) number_format(0, 1) : (double) number_format($rating, 1),
            'mobile' => $this->mobile,
            'refferid' => $this->reffer_id,
            'address' => $this->address,
            'status' => $this->status,
            'cnic' => $this->profile->cnic,
            'dob' => $this->profile->dob,
            'wallet' => $wallet ==  null ? 0 : $wallet->balance,
            'bonus' => $bonus ==  null ? 0 : $bonus->balance,
            'addresses' => $this->addresses,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
