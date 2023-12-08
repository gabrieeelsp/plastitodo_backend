<?php

namespace App\Http\Resources\v1\orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderitemResource extends JsonResource
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
            'type' => 'orderitems',
            'attributes' => [
                'cantidad' => $this->cantidad,
                'precio' => $this->precio,
                'saleproduct_id' => $this->saleproduct_id,
                'cantidad_total' => $this->cantidad_total,
                'name' => $this->saleproduct->name,
                'is_stock_unitario_variable' => $this->saleproduct->stockproduct->is_stock_unitario_variable,
                'stock_aproximado_unidad' => $this->saleproduct->stockproduct->stock_aproximado_unidad,
                'relacion_venta_stock' => $this->saleproduct->relacion_venta_stock,

                'image1'     => $this->saleproduct->image1 ? asset($this->saleproduct->image1) : null,
                'image2'     => $this->saleproduct->image2 ? asset($this->saleproduct->image2) : null,
                'image3'     => $this->saleproduct->image3 ? asset($this->saleproduct->image3) : null,

                'is_prepared' => $this->is_prepared,
            ],
            'relationships' => [
                'saleproduct' => [
                    'id' => $this->saleproduct->id,
                    'attributes' => [
                        'name' => $this->saleproduct->name,
                        'relacion_venta_stock' => $this->saleproduct->relacion_venta_stock,
                        
                        'image1'     => $this->saleproduct->image1 ? asset($this->saleproduct->image1) : null,
                        'image2'     => $this->saleproduct->image2 ? asset($this->saleproduct->image2) : null,
                        'image3'     => $this->saleproduct->image3 ? asset($this->saleproduct->image3) : null,
                    ],
                    'relationships' => [
                        'stockproduct' => [
                            'attributes' => [
                                'is_stock_unitario_variable' => $this->saleproduct->stockproduct->is_stock_unitario_variable,
                                'stock_aproximado_unidad' => $this->saleproduct->stockproduct->stock_aproximado_unidad,
                            ],
                            'relationships' => [
                                'stocksucursals' => StockproductStockSucursalResource::collection($this->saleproduct->stockproduct->stocksucursals),
                            ]
                        ]
                    ]
                ]
            ]
        ]; 
    }
}
