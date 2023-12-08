<?php

namespace App\Http\Resources\v1\tags;

use Illuminate\Http\Resources\Json\JsonResource;

class TaguserResource extends JsonResource
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
            'type' => 'users',
            'attributes' => [
                'name' => $this->name,
                'surname' => $this->surname ? [
                    $this->surname
                ] : null,
                'tipo_persona' => $this->tipo_persona, 
            ],
        ]; 
    }
}
