<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class ManDelivery extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $table = 'deliveries';
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }


    protected $guarded = ['id'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_id');
    }
}
