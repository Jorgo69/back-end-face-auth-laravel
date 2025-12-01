<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'image_url' => $this->image_url,
            'face_age' => $this->face_age,
            'face_gender' => $this->face_gender,
            'is_active' => $this->is_active,
            'registered_at' => $this->registered_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relations conditionnelles
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            
            'verifications_count' => $this->when(
                isset($this->verifications_count),
                $this->verifications_count
            ),
        ];
    }
}