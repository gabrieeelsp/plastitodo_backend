<?php

namespace App\Http\Resources\v1\stockmovements;

use Illuminate\Http\Resources\Json\JsonResource;

class StockmovementitemResource extends JsonResource
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
            ],
            'relationships' => [
                'stockproduct' => [
                    'id' => $this->stockproduct->id,
                    'attributes' => [
                        'name' => $this->stockproduct->name,
                        'costo' => $this->stockproduct->costo,
                        'is_stock_unitario_variable' => $this->stockproduct->is_stock_unitario_variable,
                        'stock_aproximado_unidad' => $this->stockproduct->stock_aproximado_unidad,
                        'image' => $this->stockproduct->image ? asset($this->stockproduct->image) : null,
                    ],
                ],
            ]
        ];
    }
}
