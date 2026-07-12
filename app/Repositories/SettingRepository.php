<?php

namespace App\Repositories;


use App\Interfaces\SettingRepositoryInterface;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

class SettingRepository extends CrudRepository implements SettingRepositoryInterface
{
    protected Model $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }
}
