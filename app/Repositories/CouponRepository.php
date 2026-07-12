<?php

namespace App\Repositories;


use App\Interfaces\CouponRepositoryInterface;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Model;

class CouponRepository extends CrudRepository implements CouponRepositoryInterface
{
    protected Model $model;

    public function __construct(Coupon $model)
    {
        $this->model = $model;
    }
}
