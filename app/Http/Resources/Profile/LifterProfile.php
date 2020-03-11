<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Profile\ProfileService;
class LifterProfile extends JsonResource
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
            'avatar' => $this->avatar == null ? null : asset("storage/".$this->avatar),
            'mobile' => $this->mobile,
            'refferid' => $this->reffer_id,
            'address' => $this->address,
            'status' => $this->status,
            'cnic' => $this->profile->cnic,
            'dob' => $this->profile->dob,
            'services' => ProfileService::collection($this->services)
        ];
    }
}
