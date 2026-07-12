<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\DeliveryRequest;
use App\Http\Resources\DeliveryResource;
use App\Http\Resources\OrderResource;
use App\Interfaces\DeliveryRepositoryInterface;
use App\Models\Delivery;
use App\Models\ManDelivery;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DeliveryController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(DeliveryRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }
    public function index()
    {
        try {
            $Delivery = DeliveryResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $Delivery->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(DeliveryRequest $request)
    {
        try {
            $data = $request->validated();

            // عمل هاش للباسورد المرسل
            $data['password'] = Hash::make($data['password']);

            $model = $this->crudRepository->create($data);

            return new DeliveryResource($model);

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function show(ManDelivery $manDelivery)
    {
        try {
            $manDelivery->load(['orders']);
            return JsonResponse::respondSuccess('Card fetched successfully', new DeliveryResource($manDelivery));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(DeliveryRequest $request, ManDelivery $manDelivery)
    {
        try {
            $data = $request->validated();

            // لو الباسورد مبعوت → نعمله hashing
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                // لو مش مبعوت → نشيله من البيانات عشان ما يبقاش null
                unset($data['password']);
            }

            $manDelivery->update($data);

            return JsonResponse::respondSuccess('Delivery updated successfully');

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request)
    {
        try {
            $count = $this->crudRepository->deleteRecords('deliveries', $request['ids']);
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
            $this->crudRepository->restoreItem(ManDelivery::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request)
    {
        try {
            $this->crudRepository->deleteRecordsFinial(ManDelivery::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }





    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $delivery = ManDelivery::where('email', $request->email)->first();

        if (!$delivery || !Hash::check($request->password, $delivery->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // إصدار توكن بشكل صحيح
        $token = $delivery->createToken('delivery-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'delivery' => new DeliveryResource($delivery),
        ]);
    }


    public function checkAuth(Request $request)
    {
        $delivery = auth()->user(); // guard للـ delivery

        if (!$delivery) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $delivery->load('orders');

        return response()->json([
            'delivery' => new DeliveryResource($delivery),
        ]);
    }


    public function assignOrder(Request $request, $orderId)
    {
        $delivery = auth()->user(); // الدليفرى اللي عامل login

        $order = Order::findOrFail($orderId);

        // تحديث الأوردر
        $order->delivery_id = $delivery->id;
        $order->delivery_status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Order assigned successfully',
            'order' => new OrderResource($order),
        ]);
    }

}
