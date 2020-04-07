<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Order;
class ProfileService extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ids = Order::where('service_id',$this->id)->where('lifter_id',$this->pivot->user_id)->where('status','confirmed')->pluck('id')->toArray();
        $rating = \App\Review::whereIn('order_id', $ids)->where('type', 'lifter')->avg('starts');
        return [
            'id' => $this->id,
            'name' => $this->s_name,
            'stars' => $rating == null ? 0 : (double) number_format($rating, 1),
            'orders' =>  count($ids),
            'total' => Order::where('service_id',$this->id)->where('lifter_id',$this->pivot->user_id)->count(),
            'deliver' => Order::where('service_id',$this->id)->where('lifter_id',$this->pivot->user_id)->where('status','delivered')->count(),
        ];
    }
}
