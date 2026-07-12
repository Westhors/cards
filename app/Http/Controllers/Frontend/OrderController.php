<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Card;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{

    public function index()
    {
        try {
            $orders = Order::with('items.card')
                ->where('user_id', auth()->id())
                ->latest()
                ->get();

            return JsonResponse::respondSuccess('Orders fetched successfully', OrderResource::collection($orders));
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders: ' . $e->getMessage());
            return JsonResponse::respondError('Failed to fetch orders');
        }
    }

    // افترض ان عندك موديل Coupon

    public function createOrder(OrderRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // رقم الطلب
            $orderNumber = 'CD' . date('Ymd') . rand(1000, 9999);

            // حساب subtotal من المنتجات
            $subtotal = 0;
            foreach ($data['cards'] as $card) {
                $product = Card::findOrFail($card['id']);
                $subtotal += $product->price * $card['qty'];
            }

            // خصم الكوبونات
            $couponDiscount = 0;
            if (!empty($data['promo_code'])) {
                $coupon = Coupon::where('code', $data['promo_code'])
                    ->where('active', true)
                    ->first();

                if ($coupon) {
                    if (in_array($coupon->type, ['percent', 'percentage'])) {
                        $couponDiscount = $subtotal * ($coupon->value / 100);
                    } elseif ($coupon->type === 'fixed') {
                        $couponDiscount = $coupon->value;
                    }
                }
            }

            // خصم 20% إذا المستخدم ACTIVE ووسيلة الدفع كاش
            $activeDiscount = 0;
            if (auth()->user()?->active && ($data['payment_type'] ?? null) === 'cash') {
                $activeDiscount = $subtotal * 0.20;
            }

            // اجمالي الخصم
            $totalDiscount = $activeDiscount + $couponDiscount;
            $total = $subtotal - $totalDiscount;

            if (!auth()->user()?->active && ($data['payment_type'] ?? null) === 'installment') {
                return response()->json([
                    'result' => 'Error',
                    'message' => 'Only active users can place orders with installment payment.'
                ], 422);
            }
            // إنشاء الطلب
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address_line' => $data['address_line'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'zip_code' => $data['zip_code'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'promo_code' => $data['promo_code'] ?? null,
                'payment_type' => $data['payment_type'] ?? null,
                'installment_months' => $data['installment_months'] ?? null,
                'increase_rate' => $data['increase_rate'] ?? null,

                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'active_discount' => $activeDiscount,
                'coupon_discount' => $couponDiscount,
                'total_amount' => $total,
            ]);

            // إضافة العناصر للطلب
            foreach ($data['cards'] as $card) {
                $order->items()->create([
                    'card_id' => $card['id'],
                    'qty' => $card['qty'],
                    'color' => $card['color'] ?? null,
                ]);
            }

            $order->loadMissing(['items.card', 'user']);

            // إنشاء PDF
            $pdf = Pdf::loadView('pdf.invoice', ['order' => $order, 'couponDiscount' => $couponDiscount,])
                ->setPaper('A4')
                ->setOption('defaultFont', 'Cairo')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true);

            $fileName = "invoices/invoice_{$order->order_number}.pdf";
            Storage::disk('public')->put($fileName, $pdf->output());
            $order->update(['invoice_pdf' => $fileName]);

            // مسح الكارت
            \App\Models\Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            return JsonResponse::respondSuccess(
                'Order created successfully',
                [
                    'order' => new OrderResource($order->load('items.card', 'user')),
                    'invoice_url' => asset('storage/' . $fileName),
                    'invoice_path' => $fileName
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return JsonResponse::respondError($e->getMessage(), 500);
        }
    }



    public function show($orderNumber)
    {
        try {
            $order = Order::with('items.card')->where('order_number', $orderNumber)->first();

            if (!$order) {
                return JsonResponse::respondError('Order not found', 404);
            }

            return JsonResponse::respondSuccess('Order fetched successfully', new OrderResource($order));
        } catch (\Exception $e) {
            Log::error('Failed to fetch order: ' . $e->getMessage());
            return JsonResponse::respondError('Failed to fetch order');
        }
    }

    public function delete($id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $order->delete();

            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function indexUser(Request $request)
    {
        try {
            $user = $request->user();

            $orders = Order::with('items.card')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return JsonResponse::respondSuccess('Orders fetched successfully', OrderResource::collection($orders));
        } catch (\Exception $e) {
            Log::error('Failed to fetch user orders: ' . $e->getMessage());
            return JsonResponse::respondError('Failed to fetch orders');
        }
    }
}
