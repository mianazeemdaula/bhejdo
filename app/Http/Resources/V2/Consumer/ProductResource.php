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
            'company' => ['id'=>$this->company->id, 'name' => $this->company->name]
        ];
    }
}
