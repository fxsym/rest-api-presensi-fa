<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'nim' => $this->nim,
            'class' => $this->class,
            'phone' => $this->phone,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'image' => $this->image,
            'imagelink' => $this->image,
            'presence_count' => $this->presences->where('status', 'validated')->count(),
            'honor' => new HonorResource($this->whenLoaded('honor'))
        ];
    }
}
