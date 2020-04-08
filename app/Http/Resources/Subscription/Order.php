<?php

namespace App\Http\Resources\Subscription;

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
            'address' => $this->address,
            'delivery_time' => $this->delivery_time,
            'address' => $this->address,
            'type' => $this->subscribe_type,
            'days' => $this->days,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
