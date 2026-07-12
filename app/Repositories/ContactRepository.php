<?php

namespace App\Repositories;


use App\Interfaces\ContactRepositoryInterface;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;

class ContactRepository extends CrudRepository implements ContactRepositoryInterface
{
    protected Model $model;

    public function __construct(Contact $model)
    {
        $this->model = $model;
    }
}
