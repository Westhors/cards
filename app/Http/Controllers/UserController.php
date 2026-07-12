<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class UserController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(UserRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $users = UserResource::collection($this->crudRepository->all([], [], ['*']));
            return $users->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function show(User $user): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item fetched successfully', new UserResource($user));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(UserRequest $request, User $user)
    {
        try {
            $this->crudRepository->update($request->validated(), $user->id);

            if ($request->hasFile('avatar')) {
                $user = User::find($user->id);
                $this->crudRepository->AddMediaCollection('avatar', $user);
            }

            activity()->performedOn($user)->withProperties(['attributes' => $user])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('users', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(User::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecordsFinial(User::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function register(UserRequest $request)
    {
        try {
            // تأكد إن عندك fillable للحقل avatar في الموديل
            $data = $request->validated();

            // خزّن الباسورد قبل إنشاء اليوزر
            $data['password'] = Hash::make($request->password);
            $data['unhashed_password'] = $request->password;
            $data['active'] = false; // تعيين المستخدم كغير مفعل في البداية
            // لو الصورة موجودة
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = asset('storage/' . $avatarPath);
            }

            // إنشاء المستخدم
            $user = $this->crudRepository->create($data);
            // توليد OTP
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->save();

            // إرسال OTP عبر الإيميل
            Mail::to($user->email)->send(new SendOtpMail($user));

            // إنشاء التوكن
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => trans(JsonResponse::MSG_ADDED_SUCCESSFULLY_APPLICATION),
                'data' => new UserResource($user),
                'token' => $token
            ]);
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        // توليد OTP جديد
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->save();

        // إرسال OTP
        Mail::to($user->email)->send(new SendOtpMail($user));

        return response()->json([
            'message' => 'تم إعادة إرسال كود التفعيل بنجاح'
        ]);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:6'
        ]);

        $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'OTP غير صحيح',
                'result' => 'Error',
            ], 400);
        }

        // بعد التأكد، امسح OTP
        $user->otp = null;
        $user->save();

        return response()->json([
            'message' => 'تم التحقق بنجاح',
            'result' => 'Success'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        // لو حابب تتأكد إن الـ OTP اتستخدم قبل كده
        if ($user->otp !== null) {
            return response()->json([
                'message' => 'يجب تأكيد OTP أولاً',
                'result' => 'Error',
            ], 403);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح',
            'result' => 'Success',
        ]);
    }


    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'result' => 'Error',
                    'data' => null,
                    'message' => 'The provided credentials are incorrect.',
                    'status' => 401,
                ], 401); // Send 401 Unauthorized
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'result' => 'Success',
                'data' => new UserResource($user),
                'message' => 'User Logged In Successfully',
                'status' => 200,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'result' => 'Error',
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function checkAuth(Request $request)
    {
        try {
            $user = Auth::user()->load('orders.items.card', 'userProducts');
            return JsonResponse::respondSuccess('User logged in successfully', new UserResource($user));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user(); // من التوكن

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about' => 'nullable|string|max:1000',
            'url' => 'nullable|url|max:255',

            'id_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'bank_statement_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'invoice_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',

            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6',
        ]);

        try {
            // تحديث البيانات العادية
            $user->fill($request->only(['name', 'email', 'phone', 'about', 'url']));

            // رفع الصور
            $imageFields = ['avatar', 'id_image', 'bank_statement_image', 'invoice_image'];

            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('uploads', 'public');
                    $user->{$field} = asset('storage/' . $path);
                }
            }

            // تغيير الباسورد
            if ($request->filled('current_password') && $request->filled('new_password')) {

                if (!Hash::check($request->current_password, $user->password)) {
                    return JsonResponse::respondError('كلمة المرور الحالية غير صحيحة', 422);
                }

                $user->password = Hash::make($request->new_password);
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return JsonResponse::respondSuccess([
                'message' => trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY),
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return JsonResponse::respondSuccess([], 'Successfully logged out');
    }

    public function deleteAccount(Request $request)
    {
        try {
            $user = $request->user(); // اليوزر الحالي

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Your account has been deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function handleCallback(Request $request)
    {
        // جلب tap_id من الرابط
        $tap_id = $request->query('tap_id');

        if (!$tap_id) {
            return response()->json(['error' => 'tap_id not found'], 400);
        }

        // استعلام حالة الدفع من Tap
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.tap.secret'),
            'Content-Type' => 'application/json',
        ])->get("https://api.tap.company/v2/charges/$tap_id");

        $charge = $response->json();

        if ($charge['status'] === 'CAPTURED') {
            return view('tap.success', compact('charge'));
        } else {
            return view('tap.failed', compact('charge'));
        }
    }
}
