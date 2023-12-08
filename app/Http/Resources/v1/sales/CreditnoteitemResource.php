<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditnoteitemResource extends JsonResource
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
            'type' => 'creditnoteitems',
            'attributes' => [
                'valor' => $this->valor,
                'descripcion' => $this->descripcion
            ],
            'relationships' => [
                'ivaaliquot' => [
                    'id' => $this->ivaaliquot_id,
                    'name' => $this->ivaaliquot->name,
                    'valor' => $this->ivaaliquot->valor
                ]
            ]
        ];
    }
}
