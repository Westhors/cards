<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends BaseModel
{
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'card_id', 'qty' , 'color'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
