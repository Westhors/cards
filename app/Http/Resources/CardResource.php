<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'slug' => $this->slug ?? null,
            'description' => $this->description ?? null,
            'short_description' => $this->short_description ?? null,
            'old_price' => $this->old_price ?? null,
            'discount' => $this->discount ?? null,
            'price' => $this->price ?? null,
            'product_number' => $this->product_number ?? null,
            'currency' => $this->currency ?? null,
            'quantity' => $this->quantity ?? null,
            'link_video' => $this->link_video ?? null,
            'image' => $this->image ?? ($this->gallery[0] ?? null),
            'gallery' => $this->gallery ?? [],

            'category' => $this->category->name ?? null,
            'brand' => $this->brand->name ?? null,
            'active' => $this->active ?? null,

            'average_rating' => round($this->average_rating, 1) ?? null,
            'reviews_count' => $this->reviews_count ?? null,
            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user_name' => $review->user->name ?? 'Anonymous',
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at->format('Y-m-d'),
                    ];
                });
            }) ?? null,
            'free_delevery' => $this->free_delevery ?? null,
            'one_year_warranty' => $this->one_year_warranty ?? null,


            'mobile' => '------------------------------------------------------',

            'type' => $this->type ?? null,
            'type_silicone' => $this->type_silicone ?? null,
            'hardness' => $this->hardness ?? null,
            'bio' => $this->bio ?? null,
            'time_in_ear' => $this->time_in_ear ?? null,
            'end_curing' => $this->end_curing ?? null,
            'viscosity' => $this->viscosity ?? null,
            'color' => $this->color ? explode(' - ', $this->color) : [],
            'packaging' => $this->packaging ?? null,
            'item_number' => $this->item_number ?? null,
            'mix_gun' => $this->mix_gun ?? null,
            'mix_canules' => $this->mix_canules ?? null,
            // 'category' => new CategoryResource($this->category),
            'createdAt' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s A') : null,
            'updatedAt' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s A') : null,
            'deletedAt' => $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i:s A') : null,
        ];
    }
}


