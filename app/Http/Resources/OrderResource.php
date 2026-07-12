<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'name' => $this->user->name,
            'email' => $this->email,
            'apartment' => $this->apartment ?? null,
            'order_number' => $this->order_number,
            'phone' => $this->phone,
            'address_line' => $this->address_line,
            'city' => $this->city,
            'state' => $this->state,
            'status' => $this->status,
            'zip_code' => $this->zip_code,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'promo_code' => $this->promo_code,

            'payment_type' => $this->payment_type ?? null,
            'installment_months' => $this->installment_months ?? null,
            'increase_rate' => $this->increase_rate ?? null,
            'total_amount' => $this->total_amount ?? null,
            'subtotal' => $this->subtotal ?? null,
            'discount' => $this->discount ?? null,
            //  'delivery_man' => $this->deliveryMan ?? null,
            'delivery_status' => $this->delivery_status ?? null,
            'delivery_name' => $this->delivery->name ?? null,
            'invoice_pdf_path' => $this->invoice_pdf,
            'invoice_pdf_url' => $this->invoice_pdf
                ? asset('storage/' . $this->invoice_pdf)
                : null,
            'cards' => OrderItemResource::collection($this->whenLoaded('items')),
            'orders' => OrderItemResource::collection($this->whenLoaded('items')),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s A'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s A'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s A'),
        ];
    }
}



