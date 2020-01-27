<?php

namespace App\Http\Resources\Milk;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Lifter;

class OrderDetail extends JsonResource
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'lifter' => new Lifter($this->lifter),
        ];
    }
}
