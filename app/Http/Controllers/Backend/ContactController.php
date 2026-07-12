<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;

use App\Helpers\JsonResponse;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ContactResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ContactRepositoryInterface;
use App\Models\Category;
use App\Models\Contact;
use Exception;
use Illuminate\Http\Request;

class ContactController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(ContactRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index(Request $request)
    {
        try {
            $Contact = ContactResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $Contact->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function store(ContactRequest $request)
    {
        try {
            $data = $request->validated();

            $model = $this->crudRepository->create($data);

            return new ContactResource($model);
        } catch (Exception $e) {
            \Log::error('Contact Store Error', ['error' => $e->getMessage()]);
            return JsonResponse::respondError($e->getMessage());
        }
    }

}
