<?php

namespace App\Http\Resources\v1\suppliers;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'type' => 'suppliers',
            'attributes' => [
                'name' => $this->name,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
                'telefono_movil' => $this->telefono_movil,
                'email' => $this->email,

                'comments' => $this->comments ? $this->comments :  '',
            ],
            'relationships' => [
                'purchaseproducts' => PurchaseproductResource::collection($this->purchaseproducts),
            ]
        ];
    }
}
