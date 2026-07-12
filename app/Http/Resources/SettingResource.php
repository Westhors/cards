<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'promotional_offer_image_one' => $this->promotional_offer_image_one,
            'title_one' => $this->title_one,
            'promotional_offer_image_two' => $this->promotional_offer_image_two,
            'title_two' => $this->title_two,
            'promotional_offer_image_three' => $this->promotional_offer_image_three,
            'title_three' => $this->title_three,
            'promotional_offer_image_four' => $this->promotional_offer_image_four,
            'title_four' => $this->title_four,
            'promotional_offer_image_five' => $this->promotional_offer_image_five,
            'title_five' => $this->title_five,
            
            'terms_and_conditions' => $this->terms_and_conditions
                ? array_values(array_filter(array_map(function ($line) {
                    return trim(str_replace("\r", "", $line));
                }, preg_split('/\r\n|\r|\n/', $this->terms_and_conditions))))
                : [],
            'created_at' => $this->created_at,
        ];
    }
}
