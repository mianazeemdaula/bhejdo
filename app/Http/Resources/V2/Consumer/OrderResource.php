<?php

namespace App\Http\Resources\V2\Consumer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Consumer;
use App\Http\Resources\User\Lifter;
use App\Http\Resources\V2\Consumer\OrderDetailResource;

class OrderResource extends JsonResource
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
            'store' => new Lifter($this->store),
            'details' => new OrderDetailResource($this->details),
            'type' => $this->type,
            'address' => $this->address,
            'charges' => $this->charges,
            'delivery_time' => $this->delivery_time,
            'note' => $this->note,
            'bonus_deduction' => $this->bonus_deduction,
            'payable_amount' => $this->payable_amount,
            'consumer_wallet' => $this->consumer_wallet,
            'status' => $this->status,
            'bullet_delivery' => $this->bullet_delivery,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
