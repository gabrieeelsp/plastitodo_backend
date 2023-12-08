<?php

namespace App\Http\Resources\v1\sales\salelist;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'type' => 'payments',
            'attributes' => [
                'valor' => $this->valor,
                'name' => $this->paymentmethod->name,
                'is_confirmed' => $this->is_confirmed,
            ],
            'relationships' => [
                'paymentmethod' => [
                    'id' => $this->paymentmethod->id,
                    'name' => $this->paymentmethod->name,
                    'is_confirmed' => $this->is_confirmed,
                    'requires_confirmation' => $this->paymentmethod->requires_confirmation,
                ]
            ]
        ];
    }
}
