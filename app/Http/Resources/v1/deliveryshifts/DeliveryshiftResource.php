<?php

namespace App\Http\Resources\v1\deliveryshifts;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryshiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => 'devliveryshifts',
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
            ],
        ];
    }
}
