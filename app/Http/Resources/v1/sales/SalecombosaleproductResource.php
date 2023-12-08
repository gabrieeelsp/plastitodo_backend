<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

class SalecombosaleproductResource extends JsonResource
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
                'cantidad' => $this->cantidad,
            ],
            'relationships' => [
                'saleproduct' => [
                    'id' => $this->saleproduct->id,
                    'name' => $this->saleproduct->name
                ] 
            ]
        ];
    }
}
