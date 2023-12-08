<?php

namespace App\Http\Resources\v1\saleproducts;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleproductVentaResource extends JsonResource
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
                'precio_min' => $this->precio_min,
                'precio_may' => $this->precio_may,
                'relacion_venta_stock' => $this->relacion_venta_stock,

                'desc_min' => $this->desc_min,
                'desc_may' => $this->desc_may,
                'fecha_desc_desde' => $this->fecha_desc_desde,
                'fecha_desc_hasta' => $this->fecha_desc_hasta,

                'image1'     => $this->image1 ? asset($this->image1) : null,
                'image2'     => $this->image2 ? asset($this->image2) : null,
                'image3'     => $this->image3 ? asset($this->image3) : null,

            ],
            'relationships' => [
                'stockproduct' => [
                    'id' => $this->stockproduct_id,
                    'type' => 'stockproducts',
                    'attributes' => [
                        'name' => $this->stockproduct->name,
                        'costo' => $this->stockproduct->costo,
                        'stock' => $this->stockproduct->getStockSucursal($request->get('sucursal')),
                        'is_stock_unitario_variable' => $this->stockproduct->is_stock_unitario_variable,
                        'stock_aproximado_unidad' => $this->stockproduct->stock_aproximado_unidad
		    ],
		    'relationships' => [
			'saleproducts' => SaleproductVentaSiblingResource::collection($this->stockproduct->saleproducts),
		    ]
                    
                ]
                
            ]
        ];
    }
}
