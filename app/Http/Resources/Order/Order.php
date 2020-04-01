<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Consumer;
use App\Http\Resources\User\Lifter;
use App\Http\Resources\Service;
use App\Http\Resources\Order\Review;
use Illuminate\Support\Facades\Auth;

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
            'charges' => $this->charges,
            'bonus' => $this->bonus_paid,
            'payable' => $this->payable_amount,
            'address' => $this->address,
            'delivery_time' => $this->delivery_time,
            'address' => $this->address,
            'type' => $this->type,
            'note' => $this->note,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status == 'collected' && Auth::check() && Auth::user()->hasRole('consumer') ? "delivered"  : $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_time' => $this->created_time,
            'accepted_time' => $this->accepted_time,
            'shipped_time' => $this->shipped_time,
            'delivered_time' => $this->delivered_time,
            'confirmed_time' => $this->confirmed_time,
            'review' => Auth::check() && Auth::user()->hasRole('consumer') ? new Review($this->lifterReview) : new Review($this->consumerReview),
        ];
    }
}
