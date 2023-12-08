<?php

namespace App\Http\Resources\v1\stocktransfers;

use Illuminate\Http\Resources\Json\JsonResource;

class StocktransferitemResource extends JsonResource
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
                'is_recibido' => $this->is_recibido,
                'is_prepared' => $this->is_prepared,
            ],
            'relationships' => [
                'stockproduct' => [
                    'id' => $this->stockproduct->id,
                    'attributes' => [
                        'name' => $this->stockproduct->name,
                        'is_stock_unitario_variable' => $this->stockproduct->is_stock_unitario_variable,
                        'stock_aproximado_unidad' => $this->stockproduct->stock_aproximado_unidad,
                        'image' => $this->stockproduct->image ? asset($this->stockproduct->image) : null,
                    ],
                ],
            ]
        ];
    }
}
