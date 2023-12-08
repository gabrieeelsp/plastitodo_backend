<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

class SalecomboitemResource extends JsonResource
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
                'precio' => $this->precio,
                'cantidad' => $this->cantidad,
            ],
            'relationships' => [
                'combo' => [
                    'id' => $this->combo->id,
                    'name' => $this->combo->name,
                ],
                'ivaaliquot' => [
                    'id' => $this->ivaaliquot_id,
                    'name' => $this->ivaaliquot->name,
                    'valor' => $this->ivaaliquot->valor
                ],
                'salecombosaleproducts' => SalecombosaleproductResource::collection($this->salecombosaleproducts),
            ]
        ];
    }
}
