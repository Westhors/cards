<?php

namespace App\Repositories;


use App\Interfaces\DeliveryRepositoryInterface;
use App\Models\ManDelivery;
use Illuminate\Database\Eloquent\Model;

class DeliveryRepository extends CrudRepository implements DeliveryRepositoryInterface
{
    protected Model $model;

    public function __construct(ManDelivery $model)
    {
        $this->model = $model;
    }
}
