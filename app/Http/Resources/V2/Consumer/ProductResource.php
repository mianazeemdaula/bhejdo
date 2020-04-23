<?php

namespace App\Http\Resources\V2\Consumer;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'category' => ['id'=>$this->category->id, 'name' => $this->category->name],
            'company' => ['id'=>$this->company->id, 'name' => $this->company->name],
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
            'min_qty_charges' => $this->min_qty_charges,
            'sale_price' => $this->sale_price,
            'markeet_price' => $this->markeet_price,
            'bonus_deduction' => $this->bonus_deduction,
            'weight' => $this->weight,
            'img_url' => $this->img_url == null ? null : asset("product/".$this->img_url),
            'unit' => $this->unit,
        ];
    }
}
