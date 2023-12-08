<?php

namespace App\Http\Resources\v1\doctypes;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctypeResource extends JsonResource
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
            'type' => 'doctypes',
            'attributes' => [
                'id_afip' => $this->id_afip,
                'name' => $this->name,
            ]
        ];
    }
}
