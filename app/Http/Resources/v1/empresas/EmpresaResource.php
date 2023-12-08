<?php

namespace App\Http\Resources\v1\empresas;

use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaResource extends JsonResource
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
            'type' => 'empresas',
            'attributes' => [
                'name' => $this->name,
                'razon_social' => $this->razon_social,
                'domicilio_comercial' => $this->domicilio_comercial,
                'cuit' => $this->cuit,
                'ing_brutos' => $this->ing_brutos,
                'fecha_inicio_act' => $this->fecha_inicio_act,
            ],
            'relationships' => [
                'ivacondition' => $this->ivacondition ? [
                    'id' => $this->ivacondition->id,
                    'attributes' => [
                        'name' => $this->ivacondition->name,
                    ],
                    
                ] : null,
                
                
            ]
        ];
    }
}
