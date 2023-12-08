<?php

namespace App\Http\Resources\v1\stockproducts;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleproductResource extends JsonResource
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
                'name' => $this->name,
                'relacion_venta_stock' => $this->relacion_venta_stock,
                'is_enable' => $this->is_enable,
                'is_enable_web' => $this->is_enable_web,

                'porc_min' => $this->porc_min,
                'precision_min' => $this->precision_min,

                'porc_may' => $this->porc_may,
                'precision_may' => $this->precision_may,
                'comments' => $this->comments ? $this->comments :  '',
            ],
            'relationships' => [
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
