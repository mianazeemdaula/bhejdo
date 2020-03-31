<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceView extends JsonResource
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
            'name' => $this->s_name,
            'nameUrdu' => $this->urdu_name,
            'icon' => $this->img_url == null ? null : asset("services/".$this->img_url),
            'price' => $this->s_price,
            'crossPrice' => $this->cross_price,
            'scale' => $this->scale,
            'description' => $this->description
        ];
    }
}
