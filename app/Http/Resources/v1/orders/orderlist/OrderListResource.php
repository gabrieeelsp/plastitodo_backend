<?php

namespace App\Http\Resources\v1\orders\orderlist;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;
class OrderListResource extends JsonResource
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
            'attributes' => [
                //'created_at' => date('d M Y - H:i', $this->created_at->timestamp),
                'created_at' => $this->created_at,
                'fecha_entrega_acordada' => $this->fecha_entrega_acordada ? Carbon::createFromFormat('Y-m-d H:i:s', $this->fecha_entrega_acordada) : null,
                'state' => $this->state,
            ],
            'relationships' => [
                'client' => $this->client ? [
                    'id' => $this->client->id,
                    'attributes' => [
                        'name' => $this->client->name,
                        'surname' => $this->client->surname,
                        'tipo_persona' => $this->client->tipo_persona,
                    ] 
                ] : null,
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
                    ] 
                ] : null,           
            ]
        ]; 
    }
}
