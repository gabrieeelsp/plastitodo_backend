<?php

namespace App\Http\Resources\v1\sucursals;

use Illuminate\Http\Resources\Json\JsonResource;

class SucursalResource extends JsonResource
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
            'type' => 'sucursal',
            'attributes' => [
                'name' => $this->name,
                'direccion' => $this->direccion,
                'punto_venta_fe' => $this->punto_venta_fe,
                'telefono' => $this->telefono,
                'telefono_movil' => $this->telefono_movil,
                'horario' => $this->horario,
            ]
             ,
            'relationships' => [
                'empresa' => [
                    'data' => [
                        'id' => $this->empresa_id,
                        'type' => 'empresas',
                        'attributes' => [
                            'name' => $this->empresa->name,
                        ]
                    ]
                ]
            ]
        ]; 
    }
}
