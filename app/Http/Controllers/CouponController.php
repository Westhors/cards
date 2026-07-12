<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Interfaces\CouponRepositoryInterface;
use App\Models\Coupon;
use Exception;
use Illuminate\Http\Request;

class CouponController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(CouponRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }
    public function index()
    {
        try {
            $coupons = CouponResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $coupons->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(CouponRequest $request)
    {
        try {
            $data = $request->validated();
            $coupon = $this->crudRepository->create($data);
            return new CouponResource($coupon);
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(Coupon $coupon)
    {
        try {
            return JsonResponse::respondSuccess('Coupon fetched successfully', new CouponResource($coupon));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(CouponRequest $request, Coupon $coupon): ?\Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();
            $this->crudRepository->update($data, $coupon->id);
            activity()
                ->performedOn($coupon)
                ->withProperties(['attributes' => $coupon])
                ->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request)
    {
        try {
            $count = $this->crudRepository->deleteRecords('coupons', $request['ids']);
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
            $this->crudRepository->restoreItem(Coupon::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request)
    {
        try {
            $this->crudRepository->deleteRecordsFinial(Coupon::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function searchByCode(Request $request)
    {
        try {
            $code = $request->query('code');

            if (!$code) {
                return JsonResponse::respondError('Coupon code is required');
            }

            $coupon = Coupon::where('code', $code)->first();

            if (!$coupon) {
                return JsonResponse::respondError('Coupon not found');
            }

            return JsonResponse::respondSuccess(
                'Coupon found successfully',
                new CouponResource($coupon)
            );

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


}
