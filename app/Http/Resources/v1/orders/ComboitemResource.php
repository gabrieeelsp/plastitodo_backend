<?php

namespace App\Http\Resources\v1\orders;

use Illuminate\Http\Resources\Json\JsonResource;

class ComboitemResource extends JsonResource
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
            'type' => 'comboitems',
            'attributes' => [
                'name' => $this->name,
                'cantidad' => $this->cantidad,
            ],
            'relationships' => [
                'saleproducts' => ComboitemsaleproductResource::collection($this->saleproducts),
                
            ]
        ];
    }
}
