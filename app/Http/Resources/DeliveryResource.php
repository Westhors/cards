<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
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

            'role' => 'delivery',
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'active' => $this->active ?? null,

            'orders' => OrderResource::collection($this->orders),

            'createdAt' => $this->created_at?->format('Y-m-d H:i:s A'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s A'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s A'),
        ];
    }
}


