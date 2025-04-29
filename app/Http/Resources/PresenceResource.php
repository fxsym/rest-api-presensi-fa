<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PresenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image,
            'lab' => $this->lab,
            'status' => $this->status,
            'note' => $this->note,
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
