<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleitemResource extends JsonResource
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
            'type' => 'saleitems',
            'attributes' => [
                'cantidad' => $this->cantidad,
                'precio' => $this->precio,
                'saleproduct_id' => $this->saleproduct_id,
                'cantidad_total' => $this->cantidad_total,
                'name' => $this->saleproduct->name,
                'is_stock_unitario_variable' => $this->saleproduct->stockproduct->is_stock_unitario_variable,
                'stock_aproximado_unidad' => $this->saleproduct->stockproduct->stock_aproximado_unidad,
                'relacion_venta_stock' => $this->saleproduct->relacion_venta_stock,
            ],
            'relationships' => [
                'ivaaliquot' => [
                    'id' => $this->ivaaliquot_id,
                    'name' => $this->ivaaliquot->name,
                    'valor' => $this->ivaaliquot->valor
                ]
            ]
        ]; 
    }
}
