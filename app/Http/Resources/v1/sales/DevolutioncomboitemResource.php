<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

class DevolutioncomboitemResource extends JsonResource
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
                'name' => $this->salecomboitem->combo->name,
                'precio' => $this->salecomboitem->precio,

            ],
            'relationships' => [
                'devolutioncombosaleproducts' => DevolutioncombosaleproductResource::collection($this->devolutioncombosaleproducts),
                'ivaaliquot' => [
                    'id' => $this->salecomboitem->ivaaliquot_id,
                    'name' => $this->salecomboitem->ivaaliquot->name,
                    'valor' => $this->salecomboitem->ivaaliquot->valor
                ],
            ]
        ];
    }
}
