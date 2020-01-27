<?php

namespace App\Http\Resources\Milk;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Consumer;
class Order extends JsonResource
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
            'consumer' => new Consumer($this->consumer),
            'qty' => $this->qty,
            'price' => $this->price,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'details' => OrderDetail::collection($this->details)
        ];
    }
}
