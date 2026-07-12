<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

use App\Helpers\JsonResponse;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryWithProductResource;
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
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $categories = Category::with(['children' => function ($query) {
                $query->where('active', true);
            }])
                ->whereNull('parent_id')
                ->where('active', true)
                ->get();

            return JsonResponse::respondSuccess('Categories fetched successfully', CategoryResource::collection($categories));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

  public function show($id): \Illuminate\Http\JsonResponse
    {
        try {

            $category = Category::where('id', $id)
                ->where('active', true)
                ->firstOrFail();

            $category->load([
                'children' => fn($q) => $q->where('active', true),
                'cards' => fn($q) => $q->where('active', true),
                'brandCards' => fn($q) => $q->where('active', true), // لو فيه علاقة brandCards
            ]);

            return JsonResponse::respondSuccess(
                'Category fetched successfully',
                new CategoryResource($category)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function onlyChildren(): \Illuminate\Http\JsonResponse
    {
        try {
        $childrenCategories = Category::whereNotNull('parent_id')
            ->where('active', true)
            ->with(['parent', 'cards']) // ⬅️ ضفت cards
            ->get();

            return JsonResponse::respondSuccess('Children categories fetched successfully', CategoryResource::collection($childrenCategories));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function onlyChildrenBySlug(string $slug): \Illuminate\Http\JsonResponse
    {
        try {
            // Get parent category by slug
            $category = Category::where('slug', $slug)
                ->where('active', true)
                ->firstOrFail();

            // Eager load children and each child's cards
            $children = $category->children()
                ->where('active', true)
                ->with('cards')
                ->get();

            return JsonResponse::respondSuccess('Children with cards fetched successfully', CategoryResource::collection($children));
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}
