<?php

namespace App\Http\Resources\Milk;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Consumer;
use App\Http\Resources\User\Lifter;
use App\Http\Resources\Service;

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
            'service' => new Service($this->service),
            'qty' => $this->qty,
            'price' => $this->price,
            'address' => $this->address,
            'delivery_time' => $this->delivery_time,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_time' => $this->created_time,
            'accepted_time' => $this->accepted_time,
            'shipped_time' => $this->shipped_time,
            'delivered_time' => $this->delivered_time,
        ];
    }
}
