<?php

namespace App\Http\Resources\V2\Consumer;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'id' => $this->lifter_id,
            'onwork' => $this->onwork,
            'last_update' => $this->last_update,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'location' => $this->location,
        ];
    }
}
