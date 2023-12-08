<?php

namespace App\Http\Resources\v1\catalogos;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogoSaleproductResource extends JsonResource
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
                'is_enable' => $this->is_enable,

                'porc_min' => $this->porc_min,
                'precio_min' => $this->precio_min,
                'precision_min' => $this->precision_min,

                'porc_may' => $this->porc_may,
                'precio_may' => $this->precio_may,
                'precision_may' => $this->precision_may,

                'desc_min' => $this->desc_min,
                'desc_may' => $this->desc_may,
                'fecha_desc_desde' => $this->fecha_desc_desde,
                'fecha_desc_hasta' => $this->fecha_desc_hasta,

                'barcode' => $this->barcode,

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
                        'is_stock_unitario_variable' => $this->stockproduct->is_stock_unitario_variable,
                        'stock_aproximado_unidad' => $this->stockproduct->stock_aproximado_unidad,
                        'stock' => $this->stockproduct->getStockTotal(),
                    ],
                    'relationships' => [
                        'familia' => $this->stockproduct->familia ? [
                            'id' => $this->stockproduct->familia->id,
                            'attributes' => [
                                'name' => $this->stockproduct->familia->name, 
                                'image'     => $this->stockproduct->familia->image ? asset($this->stockproduct->familia->image) : null,
                            ]  
                        ] : null,
                    ]
                    
                ],
                'saleproductgroup' => $this->saleproductgroup ? [
                    'id' => $this->saleproductgroup->id,
                    'attributes' => [
                        'name' => $this->saleproductgroup->name,
                    ]
                ] : null,
            ]
        ]; 
    }
}
