<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends  BaseModel
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'active' => 'boolean'
        ];
    }
    
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
}
