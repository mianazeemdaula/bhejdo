<?php

namespace App\Http\Resources\Milk;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Consumer;
use App\Http\Resources\User\Lifter;
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
            'lifter' => new Lifter($this->lifter),
            'qty' => $this->qty,
            'price' => $this->price,
            'address' => $this->address,
            'deliver_date' => $this->deliver_date,
            'deliver_time' => $this->deliver_date,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'delivery' => Delivery::collection($this->delivery)
        ];
    }
}
