<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;
use App\Helpers\JsonResponse;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(OrderRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }


    public function index()
    {
        try {
            $cards = OrderResource::collection($this->crudRepository->all(
                ['items.card', 'user'],
                [],
                ['*']
            ));
            return $cards->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    // public function index(Request $request)
    // {
    //     try {
    //         $admin = Auth::guard('admin')->user();

    //         $ordersQuery = $this->crudRepository->query()->with(['items.card', 'user']);

    //         if ($admin->role === 'supplier') {
    //             $ordersQuery->whereHas('items.card', function ($q) use ($admin) {
    //                 $q->where('supplier_id', $admin->id);
    //             });
    //         }

    //         $orders = OrderResource::collection($ordersQuery->get());

    //         return $orders->additional(JsonResponse::success());
    //     } catch (Exception $e) {
    //         return JsonResponse::respondError($e->getMessage());
    //     }
    // }

    public function show(Order $order): ?\Illuminate\Http\JsonResponse
    {
        try {
            $order->load(['items.card', 'user']);
            return JsonResponse::respondSuccess('Order Fetched Successfully', new OrderResource($order));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function orderNumber($order_number): ?\Illuminate\Http\JsonResponse
    {
        try {
            $order = Order::where('order_number', $order_number)
                ->with(['items.card', 'user'])
                ->firstOrFail();

            return JsonResponse::respondSuccess('Order Fetched Successfully', new OrderResource($order));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function changeStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string'
            ]);

            $order = Order::findOrFail($id);

            $order->status = $request->status;
            $order->save();

            return JsonResponse::respondSuccess('Order status updated successfully', new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return JsonResponse::respondError('Order not found', 404);
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request)
    {
        try {
            $count = $this->crudRepository->deleteRecords('orders', $request['ids']);

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
            $this->crudRepository->restoreItem(Order::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request)
    {
        try {
            $this->crudRepository->deleteRecordsFinial(Order::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}
