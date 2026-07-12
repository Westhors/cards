<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;
use App\Helpers\JsonResponse;
use App\Http\Requests\OfferRequest;
use App\Http\Resources\OfferResource;
use App\Interfaces\OfferRepositoryInterface;
use App\Models\Offer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfferController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(OfferRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index(Request $request)
    {
        try {
            $offers = OfferResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $offers->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function show(Offer $offer): ?\Illuminate\Http\JsonResponse
    {
        try {
            $offer->load('brand');
            return JsonResponse::respondSuccess('Item Fetched Successfully', new OfferResource($offer));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(OfferRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('uploads', 'public');
                $data['avatar'] = asset('storage/' . $path); // Full URL
            }

            $model = $this->crudRepository->create($data);

            return new OfferResource($model);
        } catch (Exception $e) {
            \Log::error('Offer Store Error', ['error' => $e->getMessage()]);
            return JsonResponse::respondError($e->getMessage());
        }
    }


        public function update(Request $request, Offer $offer)
    {
        try {
            $data = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'title_ar' => 'nullable|string|max:255',
                'description_ar' => 'nullable|string',
                'number_qty' => 'nullable|integer|min:0',
                'brand_id' => 'nullable|exists:categories,id',
                'avatar' => 'nullable|file|image|max:2048',
            ]);

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('uploads', 'public');
                $data['avatar'] = asset('storage/' . $path); // Full URL
            }

            $offer->update($data);

            return new OfferResource($offer);
        } catch (\Exception $e) {
            \Log::error('Offer Update Error', ['error' => $e->getMessage()]);
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function destroy(Request $request)
    {
        try {
            $count = $this->crudRepository->deleteRecords('offers', $request['ids']);

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
            $this->crudRepository->restoreItem(Offer::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request)
    {
        try {
            $this->crudRepository->deleteRecordsFinial(Offer::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}
