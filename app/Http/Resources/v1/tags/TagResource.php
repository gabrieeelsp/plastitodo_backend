<?php

namespace App\Http\Resources\v1\tags;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'type' => 'tags',
            'attributes' => [
                'name' => $this->name,                
		        'color' => $this->color,
                'comments' => $this->comments ? $this->comments :  '',
            ],
            'relationships' => [
                'saleproducts' => TagSaleproductResource::collection($this->saleproducts),
                'clients' => TaguserResource::collection($this->users),
            ]
        ]; 
    }
}
