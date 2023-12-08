<?php

namespace App\Http\Resources\v1\orders;

use Illuminate\Http\Resources\Json\JsonResource;

class StockproductStockSucursalResource extends JsonResource
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
                'stock' => $this->stock,
                'sucursal_id' => $this->sucursal_id,
                'stock_minimo' => $this->stock_minimo,
                'stock_maximo' => $this->stock_maximo,
            ],
        ];
    }
}
