<?php

namespace App\Repositories;


use App\Interfaces\OrderRepositoryInterface;
use App\Models\Card;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class OrderRepository extends CrudRepository implements OrderRepositoryInterface
{
    protected Model $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }
}
