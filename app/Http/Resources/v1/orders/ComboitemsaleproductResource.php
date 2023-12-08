<?php

namespace App\Http\Resources\v1\orders;

use Illuminate\Http\Resources\Json\JsonResource;

class ComboitemsaleproductResource extends JsonResource
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
            'type' => 'saleproducts',
            'attributes' => [
                'name' => $this->name,
                'relacion_venta_stock' => $this->relacion_venta_stock,

                'precio_min' => $this->precio_min,

                'precio_may' => $this->precio_may,

                'is_enable' => $this->pivot ? $this->pivot->is_enable : true,

                'image1'     => $this->image1 ? asset($this->image1) : null,
                'image2'     => $this->image2 ? asset($this->image2) : null,
                'image3'     => $this->image3 ? asset($this->image3) : null,
            ],
            'relationships' => [
                'stockproduct' => [
                    'id' => $this->stockproduct_id,
                    'type' => 'stockproducts',
                    'attributes' => [
                        'costo' => $this->stockproduct->costo,
                        'is_stock_unitario_variable' => $this->stockproduct->is_stock_unitario_variable,
                    ],
                    'relationships' => [
                        'stocksucursals' => StockproductStockSucursalResource::collection($this->stockproduct->stocksucursals),
                    ]
                    
                ],

            ]
        ];
    }
}
