<?php

namespace App\Http\Resources\v1\orders\orderchecksale;

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
            ],
            'relationships' => [
                'saleproduct' => [
                    'attributes' => [
                        'precio_min' => $this->saleproduct->precio_min,
                        'precio_may' => $this->saleproduct->precio_may,
                        'desc_min' => $this->saleproduct->desc_min,
                        'desc_may' => $this->saleproduct->desc_may,
                        'fecha_desc_desde' => $this->saleproduct->fecha_desc_desde,
                        'fecha_desc_hasta' => $this->saleproduct->fecha_desc_hasta,
                    ]
                ]
            ]
        ]; 
    }
}
