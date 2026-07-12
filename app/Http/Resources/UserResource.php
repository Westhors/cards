<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'role' => $this->role,
            'email' => $this->email,
            'age' => $this->age ?? null,
            'gender' => $this->gender ?? null,
            'country' => $this->country ?? null,
            'city' => $this->city ?? null,
            'avatar' => $this->avatar,
            'active' => $this->active,
            'is_refused' => $this->is_refused,

            'id_image' => $this->id_image,
            'bank_statement_image' => $this->bank_statement_image,
            'invoice_image' => $this->invoice_image,

            'favorites' => $this->favorites,
            'orders' => OrderResource::collection($this->orders), // ✅ هنا عرضنا الطلبات
            'products_for_sale' => UserProductResource::collection($this->whenLoaded('userProducts')),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s A'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s A'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s A'),
        ];
    }
}






