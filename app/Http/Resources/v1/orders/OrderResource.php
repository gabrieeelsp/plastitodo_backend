<?php

namespace App\Http\Resources\v1\orders;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class OrderResource extends JsonResource
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
                'is_delivery' => $this->is_delivery,
                'state' => $this->state,
                'fecha_entrega_acordada' => $this->fecha_entrega_acordada ? Carbon::createFromFormat('Y-m-d H:i:s', $this->fecha_entrega_acordada) : null,
                'cant_bultos' => $this->cant_bultos,
            ],
            'relationships' => [
                'client' => $this->client ? [
                    'id' => $this->client->id,
                    'attributes' => [
                        'name' => $this->client->name,
                        'surname' => $this->client->surname,
                        'tipo' => $this->client->tipo,
                        'tipo_persona' => $this->client->tipo_persona,
                        'direccion' => $this->client->direccion,
                        'telefono' => $this->client->telefono,
                        'coments_direccion_client' => $this->client->coments_direccion_client,
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
                'user' => [
                    'id' => $this->user->id,
                    'attributes' => [
                        'name' => $this->user->name,
                        'surname' => $this->user->surname,
                    ] 
                ],
                'sucursal' => $this->sucursal ? [
                    'id' => $this->sucursal->id,
                    'attributes' => [
                        'name' => $this->sucursal->name
                    ] 
                ] : null,
                'ivacondition' => $this->ivacondition ? [
                    'id' => $this->ivacondition->id,
                    'attributes' => [
                        'name' => $this->ivacondition->name
                    ] 
                ] : null,
                'deliveryshift' => $this->deliveryshift ? [
                    'id' => $this->deliveryshift->id,
                    'attributes' => [
                        'name' => $this->deliveryshift->name
                    ] 
                ] : null,
                'sale' => $this->sale ? [
                    'id' => $this->sale->id,
                    'attributes' => [
                        'created_at' => $this->sale->created_at,
                        'total' => $this->sale->total
                    ],
                    'relationships' => [
                        'payments' => PaymentResource::collection($this->sale->payments),
                        'refunds' => RefundResource::collection($this->sale->refunds),
                        'comprobante' => $this->sale->comprobante && $this->sale->comprobante->cae ? [
                            'id' => $this->sale->comprobante->id,
                            'attributes' => [
                                'numero' => $this->sale->comprobante->numero,
                                'punto_venta' => $this->sale->comprobante->punto_venta,
                            ]
                        ] : null,
                    ],
                ] : null,
                'orderitems' => OrderitemResource::collection($this->orderitems), 
                'ordercomboitems' => OrdercomboitemResource::collection($this->ordercomboitems),   
            ]
        ];
    }
}
