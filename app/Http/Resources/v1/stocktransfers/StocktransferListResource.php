<?php

namespace App\Http\Resources\v1\stocktransfers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class StocktransferListResource extends JsonResource
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
            'type' => 'stocktransfers',
            'attributes' => [
                'created_at' => $this->created_at,
                'estado' => $this->estado,
                'is_recibido' => $this->is_recibido,
                'recibido_at' => $this->recibido_at ? Carbon::createFromFormat('Y-m-d H:i:s',  $this->recibido_at) : null,
            ],
            'relationships' => [
                'user_origen' => [
                    'id' => $this->user_origen->id,
                    'attributes' => [
                        'name' => $this->user_origen->name
                    ] 
                ],
                'sucursal_destino' => $this->sucursal_destino ? [
                    'id' => $this->sucursal_destino->id,
                    'attributes' => [
                        'name' => $this->sucursal_destino->name
                    ],
                ] : null, 
                'user_destino' => $this->user_destino ? [
                    'id' => $this->user_destino->id,
                    'attributes' => [
                        'name' => $this->user_destino->name
                    ]
                ] : null,
                'sucursal_origen' => $this->sucursal_origen ? [
                    'id' => $this->sucursal_origen->id,
                    'attributes' => [
                        'name' => $this->sucursal_origen->name
                    ],
                ] : null,             
            ]
        ];
    }
}
