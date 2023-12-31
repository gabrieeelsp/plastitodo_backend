<?php

namespace App\Http\Resources\v1\saleproductgroups;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleproductgroupResource extends JsonResource
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
            'attributes' => [
                'name' => $this->name,
            ],
        ];
    }
}
