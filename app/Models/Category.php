<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenWithCards()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('active', true)
            ->with('cards');
    }

    public function brandCards()
    {
        return $this->hasMany(Card::class, 'brand_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

}
