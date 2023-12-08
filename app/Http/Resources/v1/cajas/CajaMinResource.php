<?php

namespace App\Http\Resources\v1\cajas;

use Illuminate\Http\Resources\Json\JsonResource;

class CajaMinResource extends JsonResource
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
            'type' => 'cajas',
            'attributes' => [
                'created_at' => $this->created_at,
                'dinero_inicial' => $this->dinero_inicial,
                'dinero_final' => $this->dinero_final,
                'is_open' => $this->is_open
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'id' => $this->user_id,
                        'type' => 'users',
                        'attributes' => [
                            'name' => $this->user->name,
                        ]
                    ]
                ],
                'sucursal' => [
                    'data' => [
                        'id' => $this->sucursal_id,
                        'type' => 'sucursals',
                        'attributes' => [
                            'name' => $this->sucursal->name,
                        ]
                    ]
                ],              
            ]
        ]; 
    }
}
