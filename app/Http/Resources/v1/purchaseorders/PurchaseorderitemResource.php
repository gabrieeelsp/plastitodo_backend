<?php

namespace App\Http\Resources\v1\purchaseorders;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseorderitemResource extends JsonResource
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
                'cantidad_total' => $this->cantidad_total,
            ],
            'relationships' => [
                'purchaseproduct' => [
                    'id' => $this->purchaseproduct->id,
                    'attributes' => [
                        'name' => $this->purchaseproduct->name,
                        'relacion_compra_stock' => $this->purchaseproduct->relacion_compra_stock,
                    ],
                    'relationships' => [
                        'stockproduct' => [
                            'id' => $this->purchaseproduct->stockproduct->id,
                            'attributes' => [
                                'costo' => $this->purchaseproduct->stockproduct->costo,
                                'is_stock_unitario_variable' => $this->purchaseproduct->stockproduct->is_stock_unitario_variable,
                                'stock_aproximado_unidad' => $this->purchaseproduct->stockproduct->stock_aproximado_unidad,
                                'image' => $this->purchaseproduct->stockproduct->image ? asset($this->purchaseproduct->stockproduct->image) : null,
                                //'stock_pedidos' => $this->purchaseproduct->stockproduct->get_stock_orders(),
                            ],
                            'relationships' => [
                                'stocksucursals' => PurchaseproductSTSucursal::collection($this->purchaseproduct->stockproduct->stocksucursals),
                            ]
                        ]
                    ]
                ],
            ]
        ];
    }
}
