<?php

namespace App\Http\Resources\v1\orders\orderchecksale;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdercomboitemResource extends JsonResource
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
                'precio' => $this->precio,
                'cantidad' => $this->cantidad,
            ],
            'relationships' => [
                'combo' => [
                    'attributes' => [
                        'name' => $this->combo->name,
                        'precio_min' => $this->combo->precio_min,
                        'precio_may' => $this->combo->precio_may,
                    ]
                ],
            ]
        ];
    }
}
