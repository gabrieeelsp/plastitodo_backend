<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'type' => 'sales',
            'attributes' => [
                //'created_at' => date('d M Y - H:i', $this->created_at->timestamp),
                'created_at' => $this->created_at,
                'total' => $this->total,
                'saldo_sale' => $this->saldo_sale,
            ],
            'relationships' => [
                'client' => $this->client ? [
                    'id' => $this->client->id,
                    'attributes' => [
			'name' => $this->client->name,
			'surname' => $this->client->surname,
                        'tipo' => $this->client->tipo
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
                        'name' => $this->user->name
                    ] 
                ],
                'sucursal' => [
                    'id' => $this->sucursal->id,
                    'attributes' => [
                        'name' => $this->sucursal->name
                    ] 
                ],
                'saleitems' => SaleitemResource::collection($this->saleitems), 
                'salecomboitems' => SalecomboitemResource::collection($this->salecomboitems),
                'payments' => PaymentResource::collection($this->payments),  
                'refunds' => RefundResource::collection($this->refunds),
                'comprobante' => $this->comprobante ? 
                    new ComprobanteSaleResource($this->comprobante)
                 : null,          
                'devolutions' => DevolutionSaleResource::collection($this->devolutions),    
                'creditnotes' => CreditnoteSaleResource::collection($this->creditnotes),     
                'debitnotes' => DebitnoteSaleResource::collection($this->debitnotes),     
            ]
        ];
    }
}
