<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends BaseModel
{
    protected $guarded = ['id'];
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'gallery' => 'array',
            'free_delevery' => 'boolean',
            'one_year_warranty' => 'boolean',
        ];
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Category::class, 'brand_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function reviews()
    {
        return $this->hasMany(CardReview::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }


}
