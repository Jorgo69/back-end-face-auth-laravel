<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaceVerificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'match_found' => $this->match_found,
            'status' => $this->status,
            'detail' => $this->detail,
            'response_time_ms' => $this->response_time_ms,
            'created_at' => $this->created_at->toISOString(),
            
            // Relations conditionnelles
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            
            'person' => $this->whenLoaded('person', fn() => [
                'id' => $this->person->id,
                'full_name' => $this->person->full_name,
            ]),
            
            // Métadonnées (optionnel)
            'metadata' => $this->when($request->user()?->isAdmin ?? false, $this->metadata),
            'ip_address' => $this->when($request->user()?->isAdmin ?? false, $this->ip_address),
        ];
    }
}