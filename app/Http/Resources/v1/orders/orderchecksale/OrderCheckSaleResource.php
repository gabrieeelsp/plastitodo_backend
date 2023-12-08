<?php

namespace App\Http\Resources\v1\orders\orderchecksale;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderCheckSaleResource extends JsonResource
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
            'type' => 'orders',
            'relationships' => [
                'client' => $this->client ? [
                    'id' => $this->client->id,
                    'attributes' => [
                        'tipo' => $this->client->tipo,
                    ],
                    'relationships' => [
                        'ivacondition' => $this->client->ivacondition ? [
                            'id' => $this->client->ivacondition->id,
                            'attributes' => [
                                'name' => $this->client->ivacondition->name,
                            ] 
                        ] : null,
                    ]
                ] : null,
                'orderitems' => OrderitemResource::collection($this->orderitems), 
                'ordercomboitems' => OrdercomboitemResource::collection($this->ordercomboitems),   
            ]
        ];
    }
}
