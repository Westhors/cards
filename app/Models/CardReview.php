<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardReview extends Model
{
    protected $fillable = [
        'card_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
