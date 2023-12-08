<?php

namespace App\Http\Resources\v1\familias;

use Illuminate\Http\Resources\Json\JsonResource;

class FamiliaResource extends JsonResource
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
            'type' => 'familias',
            'attributes' => [
                'name' => $this->name,
                'image'     => $this->image ? asset($this->image) : null,

                'comments' => $this->comments ? $this->comments :  '',
            ],
        ];
    }
}
