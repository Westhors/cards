<?php

namespace App\Models;

class Order extends BaseModel
{
    protected $table = 'orders';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery()
    {
        return $this->belongsTo(ManDelivery::class, 'delivery_id');
    }

}
