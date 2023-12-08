<?php

namespace App\Http\Resources\v1\purchaseorders;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseorderListResource extends JsonResource
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
            'type' => 'purchaseorders',
            'attributes' => [
                'created_at' => $this->created_at,
                'estado' => $this->estado,
            ],
            'relationships' => [
                'user' => [
                    'id' => $this->user->id,
                    'attributes' => [
                        'name' => $this->user->name
                    ] 
                ],
                'sucursal' => $this->sucursal ? [
                    'id' => $this->sucursal->id,
                    'attributes' => [
                        'name' => $this->sucursal->name
                    ],
                ] : null,
                'supplier' => [
                    'id' => $this->supplier->id,
                    'type' => 'suppliers',
                    'attributes' => [
                        'name' => $this->supplier->name,
                        'direccion' => $this->supplier->direccion,
                        'telefono' => $this->supplier->telefono,
                        'email' => $this->supplier->email,
                    ]                    
                ],
                
            ]
        ];
    }
}
