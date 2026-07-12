<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;

use App\Helpers\JsonResponse;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(CategoryRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

public function index(Request $request)
{
    try {
        $filters = $request->input('filters', []);
        $orderBy = $request->input('orderBy', 'id');
        $orderByDirection = $request->input('orderByDirection', 'asc');
        $perPage = $request->input('perPage', 10);
        $paginate = $request->boolean('paginate', true);

        // delete filter
        $delete = $request->input('deleted', false);

        $query = Category::whereNull('parent_id')
                        ->with(['parent', 'children']);

        /*
        |--------------------------------------------------------------------------
        | Soft Delete Filter
        |--------------------------------------------------------------------------
        */
        if ($delete === true || $delete === "true" || $delete == 1) {
            // المحذوف فقط
            $query->onlyTrashed();
        } elseif ($delete === 'all') {
            // الكل (محذوف + غير محذوف)
            $query->withTrashed();
        }
        // غير ذلك → العناصر العادية فقط (default)

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['name_ar'])) {
            $query->where('name_ar', 'like', '%' . $filters['name_ar'] . '%');
        }

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        /*
        |--------------------------------------------------------------------------
        | Order
        |--------------------------------------------------------------------------
        */
        $query->orderBy($orderBy, $orderByDirection);

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        if ($paginate) {
            $categories = $query->paginate($perPage);
        } else {
            $categories = $query->get();
        }

        return response()->json([
            'data' => CategoryResource::collection(
                $paginate ? $categories->items() : $categories
            ),
            'links' => $paginate ? [
                'first' => $categories->url(1),
                'last' => $categories->url($categories->lastPage()),
                'prev' => $categories->previousPageUrl(),
                'next' => $categories->nextPageUrl(),
            ] : null,
            'meta' => $paginate ? [
                'current_page' => $categories->currentPage(),
                'from' => $categories->firstItem(),
                'last_page' => $categories->lastPage(),
                'links' => $categories->linkCollection(),
                'path' => $categories->path(),
                'per_page' => $categories->perPage(),
                'to' => $categories->lastItem(),
                'total' => $categories->total(),
            ] : null,
            'result' => 'Success',
            'message' => 'Categories fetched successfully',
            'status' => 200,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'result' => 'Error',
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}



    public function show(Category $category): ?\Illuminate\Http\JsonResponse
    {
        try {
            $category->load('children');
            return JsonResponse::respondSuccess('Item Fetched Successfully', new CategoryResource($category));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(CategoryRequest $request)
    {
        try {
            $data = $request->validated();

     if ($request->hasFile('image')) {
                $data['icon'] = $request->file('image')->store('icons', 'public');
            }

            $model = $this->crudRepository->create($data);

            return new CategoryResource($model);
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(CategoryRequest $request, Category $category): ?\Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();

            // نفس فكرة store
            if ($request->hasFile('image')) {
                $data['icon'] = $request->file('image')->store('icons', 'public');
            }

            $this->crudRepository->update($data, $category->id);

            activity()
                ->performedOn($category)
                ->withProperties(['attributes' => $category])
                ->log('update');

            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request)
    {
        try {
            $count = $this->crudRepository->deleteRecords('categories', $request['ids']);
            return $count > 1
                ? JsonResponse::respondError(trans(JsonResponse::MSG_CANNOT_DELETED_MULTI_RESOURCE))
                : ($count == 222 ? JsonResponse::respondError(trans(JsonResponse::MSG_CANNOT_DELETED))
                    : JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY)));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        try {
            $this->crudRepository->restoreItem(Category::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request)
    {
        try {
            $this->crudRepository->deleteRecordsFinial(Category::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


public function brandIndex(Request $request)
{
    try {
        $filters = $request->input('filters', []);
        $orderBy = $request->input('orderBy', 'id');
        $orderByDirection = $request->input('orderByDirection', 'asc');
        $perPage = $request->input('perPage', 10);
        $paginate = $request->boolean('paginate', true);

        // delete filter
        $delete = $request->input('deleted', false);

        $query = Category::whereNotNull('parent_id');

        /*
        |--------------------------------------------------------------------------
        | Soft Delete Filter
        |--------------------------------------------------------------------------
        */
        if ($delete === true || $delete === "true" || $delete == 1) {
            $query->onlyTrashed();        // المحذوف فقط
        } elseif ($delete === 'all') {
            $query->withTrashed();        // الكل
        }
        // الافتراضي → غير المحذوف

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['name_ar'])) {
            $query->where('name_ar', 'like', '%' . $filters['name_ar'] . '%');
        }

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        /*
        |--------------------------------------------------------------------------
        | Order
        |--------------------------------------------------------------------------
        */
        $query->orderBy($orderBy, $orderByDirection);

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        if ($paginate) {
            $brands = $query->paginate($perPage);

            return response()->json([
                'data' => CategoryResource::collection($brands->items()),
                'links' => [
                    'first' => $brands->url(1),
                    'last' => $brands->url($brands->lastPage()),
                    'prev' => $brands->previousPageUrl(),
                    'next' => $brands->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $brands->currentPage(),
                    'from' => $brands->firstItem(),
                    'last_page' => $brands->lastPage(),
                    'links' => $brands->linkCollection(),
                    'path' => $brands->path(),
                    'per_page' => $brands->perPage(),
                    'to' => $brands->lastItem(),
                    'total' => $brands->total(),
                ],
                'result' => 'Success',
                'message' => 'Brands fetched successfully',
                'status' => 200,
            ]);
        }

        $brands = $query->get();

        return response()->json([
            'data' => CategoryResource::collection($brands),
            'links' => null,
            'meta' => null,
            'result' => 'Success',
            'message' => 'Brands fetched successfully',
            'status' => 200,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'result' => 'Error',
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}




}
