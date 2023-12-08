<?php

namespace App\Http\Resources\v1\purchaseproducts;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseproductResource extends JsonResource
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
            'type' => 'purchaseproducts',
            'attributes' => [
                'name' => $this->name,
                'relacion_compra_stock' => $this->relacion_compra_stock,
                'is_enable' => $this->is_enable,
                'codigo' => $this->codigo,
                'rel_precio_codigo' => $this->rel_precio_codigo,
            ],
            'relationships' => [
                'stockproduct' => [
                    'id' => $this->stockproduct_id,
                    'type' => 'stockproducts',
                    'attributes' => [
                        'name' => $this->stockproduct->name,
                        'costo' => $this->stockproduct->costo,
                        'is_stock_unitario_variable' => $this->stockproduct->is_stock_unitario_variable,
                        'stock_aproximado_unidad' => $this->stockproduct->stock_aproximado_unidad,
                    ]
                    
                ],
                'supplier' => [
                    'id' => $this->supplier,
                    'type' => 'suppliers',
                    'attributes' => [
                        'name' => $this->supplier->name,
                    ]
                    
                ],
            ]
        ];
    }
}
