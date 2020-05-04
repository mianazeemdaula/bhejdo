<?php

namespace App\Http\Resources\V2\Consumer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Consumer;
use App\Http\Resources\User\Lifter;
use App\Http\Resources\V2\Consumer\OrderDetailResource;

class SubscriptionResource extends JsonResource
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
            'order' => new OrderResource($this->order),
            'deliveryTime' => $this->delivery_time,
            'subscriptionType' => $this->subscribe_type,
            'days' => json_encode($this->days),
            'shift' => $this->shift,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
