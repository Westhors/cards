<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'userName' => $this->user_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'companyName' => $this->company_name,
            'role' => $this->role,
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            // 'favorites' => $this->favorites,
            'cards' => $this->cards ? CardResource::collection($this->cards) : [],
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s A'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s A'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s A'),
        ];
    }
}
