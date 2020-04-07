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
        $ids = $this->orders()->where('status','confirmed')->orWhere('status','collected')->pluck('id')->toArray();
        $rate = \App\Review::whereIn('order_id', $ids)->where('type', 'lifter')->avg('starts');
        return [
            'id' => $this->id,
            'name' => $this->s_name,
            'stars' => $rate == null ? 0 : (double) number_format($rating, 1),
            'orders' =>  count($ids),
            'total' => $this->orders()->count(),
            'deliver' => $this->orders()->where('status','delivered')->count(),
        ];
    }
}
