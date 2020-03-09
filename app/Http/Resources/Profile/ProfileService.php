<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Resources\Json\JsonResource;

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
        $ids = $this->orders()->where('status','confirmed')->pluck('id')->toArray();
        $rate = \App\Review::whereIn('order_id', $ids)->avg('starts');
        return [
            'id' => $this->id,
            'name' => $this->s_name,
            'stars' => $rate == null ? 0 : $rate,
            'orders' => count($ids)
        ];
    }
}
