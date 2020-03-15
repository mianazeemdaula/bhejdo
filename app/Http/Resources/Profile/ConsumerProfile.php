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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar == null ? null : asset("storage/".$this->avatar),
            'mobile' => $this->mobile,
            'refferid' => $this->reffer_id,
            'address' => $this->address,
            'status' => $this->status,
            'cnic' => $this->profile->cnic,
            'dob' => $this->profile->dob,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'rating' => $rating == null ? 0.0 : $rating
        ];
    }
}