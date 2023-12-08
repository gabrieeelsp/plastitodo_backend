<?php

namespace App\Http\Resources\v1\sales;

use Illuminate\Http\Resources\Json\JsonResource;

class DebitnoteSaleResource extends JsonResource
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
            'type' => 'debitnotes',
            'attributes' => [
                //'created_at' => date('d M Y - H:i', $this->created_at->timestamp),
                'created_at' => $this->created_at,
                'total' => $this->total
            ],
            'relationships' => [
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
                'debitnoteitems' => DebitnoteitemResource::collection($this->debitnoteitems),
                'comprobante' => $this->comprobante ? 
                    new ComprobanteSaleResource($this->comprobante)
                 : null, 
                
            ]
        ]; 
    }
}
