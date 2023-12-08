<?php

namespace App\Http\Resources\v1\orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdercombosaleproductResource extends JsonResource
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
            'attributes' => [
                'cantidad' => $this->cantidad,
                'is_prepared' => $this->is_prepared,
            ],
            'relationships' => [
                'saleproduct' => [
                    'id' => $this->saleproduct->id,
                    'name' => $this->saleproduct->name,
                    'attributes' => [
                        'name' => $this->saleproduct->name,
                        'image1'     => $this->saleproduct->image1 ? asset($this->saleproduct->image1) : null,
                        'image2'     => $this->saleproduct->image2 ? asset($this->saleproduct->image2) : null,
                        'image3'     => $this->saleproduct->image3 ? asset($this->saleproduct->image3) : null,  
                    ]
                ]

            ]
        ];
    }
}
