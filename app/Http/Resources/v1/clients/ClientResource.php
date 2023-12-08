<?php

namespace App\Http\Resources\v1\clients;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'type' => 'clients',
            'attributes' => [
                'name' => $this->name,
                
		        'surname' => $this->surname,

                'tipo' => $this->tipo,

                'tipo_persona' => $this->tipo_persona,

                'nombre_fact' => $this->nombre_fact,
                'direccion_fact' => $this->direccion_fact,
                'is_fact_default' => $this->is_fact_default,
                'direccion' => $this->direccion,
                
                'telefono' => $this->telefono,
                'docnumber' => $this->docnumber,
                
                'saldo' => $this->saldo,
                'credito_disponible' => $this->credito_disponible,

                'coments_client' => $this->coments_client ? $this->coments_client :  '',
                'coments_direccion_client' => $this->coments_direccion_client ? $this->coments_direccion_client :  '',

            ],
            'relationships' => [
                'ivacondition' => $this->ivacondition ? [
                    'id' => $this->ivacondition->id,
                    'attributes' => [
                        'id_afip' => $this->ivacondition->id_afip,
                        'name' => $this->ivacondition->name,
                    ],
                    'relationships' => [
                        'modelofact' => $this->ivacondition->modelofact ? [
                            'id' => $this->ivacondition->modelofact->id,
                            'attributes' => [
                                'name' => $this->ivacondition->modelofact->name
                            ] 
                        ] : null
                    ]
                ] : null,
                'doctype' => $this->doctype ? [
                    'id' => $this->doctype->id,
                    'attributes' => [
                        'id_afip' => $this->doctype->id_afip,
                        'name' => $this->doctype->name,
                    ] 
                ] : null,

                'tags' => ClientTagResource::collection($this->tags),
                'catalogos' => ClientCatalogoResource::collection($this->catalogos),
                
            ]
        ]; 
    }
}
