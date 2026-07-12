<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;
use App\Helpers\JsonResponse;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\CardResource;
use App\Http\Resources\DeliveryResource;
use App\Http\Resources\OrderResource;
use App\Interfaces\AdminRepositoryInterface;
use App\Models\Admin;
use App\Models\Card;
use App\Models\ManDelivery;
use App\Models\Order;
use App\Models\Offer;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends  BaseController
{
    protected mixed $crudRepository;

    public function __construct(AdminRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }
    public function index()
    {
        try {
            $admins = AdminResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $admins->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(AdminRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('admins', 'public');
            }

            $data['admin_id'] = auth()->id();

            $admin = $this->crudRepository->create($data);

            return new AdminResource($admin);
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(Admin $admin)
    {
        try {
            $admin->load(['category', 'brand']);
            return JsonResponse::respondSuccess('Card fetched successfully', new AdminResource($admin));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(AdminUpdateRequest $request, Admin $admin)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('admins', 'public');
            }

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $admin->update($data);

            return JsonResponse::respondSuccess('admin updated successfully');
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    // public function usersWithCards()
    // {
    //     $users = User::with('cards')->get();

    //     return JsonResponse::respondSuccess('Users with their cards', $users);
    // }






    public function register(AdminRequest $request)
    {
        try {
            $admin = $this->crudRepository->create($request->validated());

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                $admin->update(['logo' => $logoPath]);
            }

            DB::table('admins')->where('id', $admin->id)->update([
                'password' => Hash::make($request->password),
                'logo' => $logoPath ?? null,
            ]);
            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => trans(JsonResponse::MSG_ADDED_SUCCESSFULLY_APPLICATION),
                'data' => new adminResource($admin),
                'token' => $token
            ]);
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

        /** ============================
         *  1) محاوله تسجيل دخول أدمن
         *  ============================ */
        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {

            // إنشاء توكن للأدمن
            $token = $admin->createToken('admin_token')->plainTextToken;

            return response()->json([
                'type' => 'admin',
                'data' => new adminResource($admin),
                'token' => $token
            ]);
        }

        /** ============================
         *  2) محاوله تسجيل دخول دليفرى
         *  ============================ */
        $delivery = ManDelivery::where('email', $request->email)->where('active',1)->first();

        if ($delivery && Hash::check($request->password, $delivery->password)) {

            // توكن الدلـيفـري
            $token = $delivery->createToken('delivery_token')->plainTextToken;

            // pending orders
            $pendingOrders = Order::where('status', 'pending')->get();

            return response()->json([
                'type' => 'delivery',
                'data' => new DeliveryResource($delivery),
                'token' => $token,
                'orders_pending' => OrderResource::collection($pendingOrders),
            ]);
        }

        /** ============================
         *  لو ملقاش في الاتنين
         *  ============================ */
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

public function checkAuth(Request $request)
{
    try {

         $delivery = auth()->user(); // guard للـ delivery
        if ($delivery && $delivery instanceof \App\Models\ManDelivery) {

            return response()->json([
                'delivery' => new DeliveryResource($delivery),
                'user_type' => 'delivery',
            ]);
        }


        // Admin
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            return JsonResponse::respondSuccess(
                'Admin authenticated successfully',
                new AdminResource($admin)
            );
        }



        return JsonResponse::respondError('Unauthorized', 401);

    } catch (\Exception $e) {
        return JsonResponse::respondError($e->getMessage());
    }
}


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return JsonResponse::respondSuccess([], 'Successfully logged out');
    }



    public function getCounts()
    {
        $offersCount = Offer::count();
        $cardsCount = Card::count();
        $ordersCount = Order::count();

        return response()->json([
            'offers' => $offersCount,
            'cards' => $cardsCount,
            'orders' => $ordersCount,
        ]);
    }




    public function monthlyReport()
    {
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        // Users per month
        $usersPerMonthRaw = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('count', 'month');

        // Orders per month by status
        $ordersPerMonthRaw = Order::select(
            DB::raw('MONTH(created_at) as month'),
            'status',
            DB::raw('COUNT(*) as count')
        )
        ->groupBy(DB::raw('MONTH(created_at)'), 'status')
        ->get()
        ->groupBy('month');

        // Total orders per month (all statuses)
        $totalOrdersPerMonthRaw = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('count', 'month');

        // Offers per month
        $offersPerMonthRaw = Offer::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('count', 'month');

        $usersPerMonth = [];
        $ordersPendingPerMonth = [];
        $ordersConfirmedPerMonth = [];
        $ordersCancelledPerMonth = [];
        $totalOrdersPerMonth = [];
        $offersPerMonth = [];

        $totalUsers = 0;
        $totalPending = 0;
        $totalConfirmed = 0;
        $totalCancelled = 0;
        $totalOrders = 0;
        $totalOffers = 0;

        foreach ($monthNames as $num => $name) {
            // Users
            $countUsers = $usersPerMonthRaw[$num] ?? 0;
            $usersPerMonth[$name] = $countUsers;
            $totalUsers += $countUsers;

            // Orders by status
            $monthOrders = $ordersPerMonthRaw[$num] ?? collect();

            $countPending = $monthOrders->where('status','pending')->first()->count ?? 0;
            $ordersPendingPerMonth[$name] = $countPending;
            $totalPending += $countPending;

            $countConfirmed = $monthOrders->where('status','confirmed')->first()->count ?? 0;
            $ordersConfirmedPerMonth[$name] = $countConfirmed;
            $totalConfirmed += $countConfirmed;

            $countCancelled = $monthOrders->where('status','cancelled')->first()->count ?? 0;
            $ordersCancelledPerMonth[$name] = $countCancelled;
            $totalCancelled += $countCancelled;

            // Total orders (all statuses)
            $countTotalOrders = $totalOrdersPerMonthRaw[$num] ?? 0;
            $totalOrdersPerMonth[$name] = $countTotalOrders;
            $totalOrders += $countTotalOrders;

            // Offers
            $countOffers = $offersPerMonthRaw[$num] ?? 0;
            $offersPerMonth[$name] = $countOffers;
            $totalOffers += $countOffers;
        }

        return response()->json([
            'result' => 'Success',
            'message' => 'Monthly reports',
            'data' => [
                'users_per_month' => $usersPerMonth,
                'orders_pending_per_month' => $ordersPendingPerMonth,
                'orders_confirmed_per_month' => $ordersConfirmedPerMonth,
                'orders_cancelled_per_month' => $ordersCancelledPerMonth,
                'total_orders_per_month' => $totalOrdersPerMonth,
                'offers_per_month' => $offersPerMonth,
                'TOTAL' => [
                    'users' => $totalUsers,
                    'orders_pending' => $totalPending,
                    'orders_confirmed' => $totalConfirmed,
                    'orders_cancelled' => $totalCancelled,
                    'total_orders' => $totalOrders,
                    'offers' => $totalOffers,
                ]
            ]
        ]);
    }




}
