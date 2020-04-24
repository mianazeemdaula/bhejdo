<?php

namespace App\Http\Resources\V2\Consumer;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'name' => $this->name,
            'urduname' => $this->urdu_name,
            'consumer' => new Consumer($this->consumer),
            'lifter' => new Lifter($this->lifter),
            'store' => new Lifter($this->store),
            'store' => new Lifter($this->details),
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
            'min_qty_charges' => $this->min_qty_charges,
            'sale_price' => $this->sale_price,
            'markeet_price' => $this->markeet_price,
            'bonus_deduction' => $this->bonus_deduction,
            'weight' => $this->weight,
            'status' => $this->unit,
        ];
    }
}
