<?php

namespace App\Http\Resources\v1\valors;

use Illuminate\Http\Resources\Json\JsonResource;

class ValorResource extends JsonResource
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
            'type' => 'valors',
            'attributes' => [
                'name' => $this->name,
                'valor' => $this->valor,                
            ]
        ];
    }
}
