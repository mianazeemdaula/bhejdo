<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class Lifter extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $rating = \App\Order::leftJoin('reviews','reviews.order_id','=','orders.id')->where('orders.lifter_id',$this->id)->where('reviews.type','lifter')->avg('reviews.starts');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar == null ? null : asset("storage/".$this->avatar),
            'rating' => $rating == null ? 0.0 : $rating
        ];
    }
}
