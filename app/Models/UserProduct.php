<?php

namespace App\Models;

class UserProduct extends BaseModel
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
