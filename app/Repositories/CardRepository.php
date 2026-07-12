<?php

namespace App\Repositories;


use App\Interfaces\CardRepositoryInterface;
use App\Models\Card;
use Illuminate\Database\Eloquent\Model;

class CardRepository extends CrudRepository implements CardRepositoryInterface
{
    protected Model $model;

    public function __construct(Card $model)
    {
        $this->model = $model;
    }
}
