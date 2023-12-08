<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

class DevolutionitemResource extends JsonResource
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
            'type' => 'devolutionitems',
            'attributes' => [
                'cantidad' => $this->cantidad,
                'precio' => $this->saleitem->precio,
                'saleitem_id' => $this->saleitem_id,
                'is_stock_unitario_variable' => $this->saleitem->saleproduct->stockproduct->is_stock_unitario_variable,
                'cantidad_total' => $this->cantidad_total,
                'name' => $this->saleitem->saleproduct->name
            ],
            'relationships' => [
                'ivaaliquot' => [
                    'id' => $this->saleitem->ivaaliquot_id,
                    'name' => $this->saleitem->ivaaliquot->name,
                    'valor' => $this->saleitem->ivaaliquot->valor
                ]
            ]
        ]; 
    }
}
