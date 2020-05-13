<?php

namespace App\Http\Resources\V2\Consumer;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'promo_code' => $this->promo_code,
            'amount' => $this->amount,
            'type' => $this->type,
            'category' => $this->category,
            'expiry_date' => $this->expiry_date,
            'status' => (bool) $this->status,
            'shopping_limit' => $this->shopping_limit,
            'credit' => $this->credit,
        ];
    }
}
