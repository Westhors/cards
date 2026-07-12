<?php

use App\Http\Controllers\CountryShippingController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\CardController as BackendCardController;
use App\Http\Controllers\Frontend\CardController as FrontendCardController;

use App\Http\Controllers\Backend\CategoryController as BackendCategoryController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Frontend\CategoryController as FrontendCategoryController;

use App\Http\Controllers\Backend\OfferController as BackendOfferController;
use App\Http\Controllers\Backend\OrderController as BackendOrderController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\Frontend\{
    FavoriteController,
    CartController,
    OfferController,
    OrderController
};
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('register/admin', [AdminController::class, 'register']);
Route::post('login/admin', [AdminController::class, 'login']);
Route::post('admin/{admin}', [AdminController::class, 'update']);

// Route::post('order/admin', [AdminController::class, 'changeStatus']);



Route::post('orders/change-status/{id}', [BackendOrderController::class, 'changeStatus']);
// Route::get('back/orders/{order_number}', [BackendOrderController::class, 'orderNumber']);


Route::post('back/delivery/login', [DeliveryController::class, 'login']);
Route::middleware(['auth:sanctum'])->prefix('back')->group(function () {
    Route::put('delivery/order/{orderId}/assign', [DeliveryController::class, 'assignOrder']);
    Route::get('check-auth/admin', [DeliveryController::class, 'checkAuth']);
});

Route::middleware(['auth:sanctum'])->prefix('back')->group(function () {
    Route::post('logout/admin', [AdminController::class, 'logout']);
    Route::get('check-auth/admin', [AdminController::class, 'checkAuth']);
    Route::get('/dashboard/counts', [AdminController::class, 'getCounts']);


    Route::post('user/index', [UserController::class, 'index']);  // Changed POST to GET
    Route::post('user/restore', [UserController::class, 'restore']);
    Route::delete('user/delete', [UserController::class, 'destroy']);
    Route::delete('user/force-delete', [UserController::class, 'forceDelete']);
    Route::put('user/{id}/{column}', [UserController::class, 'toggle']);
    Route::post('user/{user}', [UserController::class, 'update']);
    Route::apiResource('user', UserController::class);


    Route::post('man-delivery/index', [DeliveryController::class, 'index']);  // Changed POST to GET
    Route::post('man-delivery/restore', [DeliveryController::class, 'restore']);
    Route::delete('man-delivery/delete', [DeliveryController::class, 'destroy']);
    Route::delete('man-delivery/force-delete', [DeliveryController::class, 'forceDelete']);
    Route::put('man-delivery/{id}/{column}', [DeliveryController::class, 'toggle']);
    Route::post('man-delivery/{manDelivery}', [DeliveryController::class, 'update']);
    Route::apiResource('man-delivery', DeliveryController::class);


    Route::post('brands/index', [BackendCategoryController::class, 'brandIndex']);  // Changed POST to GET
    Route::post('brands/restore', [BackendCategoryController::class, 'restore']);
    Route::delete('brands/delete', [BackendCategoryController::class, 'destroy']);
    Route::delete('brands/force-delete', [BackendCategoryController::class, 'forceDelete']);
    Route::put('brands/{id}/{column}', [BackendCategoryController::class, 'toggle']);
    Route::put('brands/{category}', [BackendCategoryController::class, 'update']);
    Route::get('brands-list', [FrontendCategoryController::class, 'onlyChildren']);
    Route::get('brands/{category}', [BackendCategoryController::class, 'show']);
    Route::post('brands', [BackendCategoryController::class, 'store']);

    Route::post('categories/index', [BackendCategoryController::class, 'index']);  // Changed POST to GET
    Route::post('categories/restore', [BackendCategoryController::class, 'restore']);
    Route::delete('categories/delete', [BackendCategoryController::class, 'destroy']);
    Route::delete('categories/force-delete', [BackendCategoryController::class, 'forceDelete']);
    Route::put('categories/{id}/{column}', [BackendCategoryController::class, 'toggle']);
    Route::post('categories/{category}', [BackendCategoryController::class, 'update']);
    Route::apiResource('categories', BackendCategoryController::class);


    Route::post('orders/index', [BackendOrderController::class, 'index']);
    Route::apiResource('orders', BackendOrderController::class);

    Route::prefix('offers')->group(function () {
        Route::post('index', [BackendOfferController::class, 'index']);
        Route::get('{offer}', [BackendOfferController::class, 'show']);
        Route::post('/', [BackendOfferController::class, 'store']);
        Route::post('{offer}', [BackendOfferController::class, 'update']);
        Route::delete('delete', [BackendOfferController::class, 'destroy']);
        Route::delete('force-delete', [BackendOfferController::class, 'forceDelete']);
        Route::post('restore', [BackendOfferController::class, 'restore']);
        Route::put('{id}/{column}', [BackendOfferController::class, 'toggle']);
    });


    Route::post('card/index', [BackendCardController::class, 'index']);
    Route::post('card/restore', [BackendCardController::class, 'restore']);
    Route::delete('card/delete', [BackendCardController::class, 'destroy']);
    Route::delete('card/force-delete', [BackendCardController::class, 'forceDelete']);
    Route::put('card/{id}/{column}', [BackendCardController::class, 'toggle']);
    Route::get('card/dashboard/users-cards', [BackendCardController::class, 'usersWithCards']);
    Route::apiResource('card', BackendCardController::class);
});

Route::prefix('front')->group(function () {
    // Front End Auth

    Route::post('login', [UserController::class, 'login']);
    Route::post('register', [UserController::class, 'register']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('check-auth', [UserController::class, 'checkAuth']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('logout', [UserController::class, 'logout']);
    });

    Route::get('cards', [FrontendCardController::class, 'index']);
    Route::get('cards/{id}', [FrontendCardController::class, 'show']);
    Route::get('search-cards', [FrontendCardController::class, 'searchByName']);
    Route::get('categories/brands/{slug}', [FrontendCardController::class, 'getByBrandSlug']);

    Route::get('categories', [FrontendCategoryController::class, 'index']);
    Route::get('categories/{id}', [FrontendCategoryController::class, 'show']);

    Route::get('brands', [FrontendCategoryController::class, 'onlyChildren']);
    Route::get('brands/{slug}', [FrontendCategoryController::class, 'onlyChildrenBySlug']);

    Route::get('latest-offer', [OfferController::class, 'latestOffer']);
    Route::get('offers', [OfferController::class, 'offers']);

    // Routes that require authentication
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('cart', [CartController::class, 'handleCart']);
        Route::get('cart', [CartController::class, 'getCartItems']);
        Route::post('favorite', [FavoriteController::class, 'handleFavorite']);
        Route::get('favorite', [FavoriteController::class, 'getFavorites']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::post('create-order', [OrderController::class, 'createOrder']);
        Route::get('order/{order_number}', [OrderController::class, 'show']);
        Route::delete('order/{id}', [OrderController::class, 'delete']);
        Route::post('cards/{id}/review', [FrontendCardController::class, 'addReview']);
    });
});


Route::post('back/countries-shipping/index', [CountryShippingController::class, 'index']);
Route::post('countries-shipping/index', [CountryShippingController::class, 'index']);
Route::apiResource('back/countries-shipping', CountryShippingController ::class);


Route::middleware('auth:sanctum')->delete('/user/delete', [UserController::class, 'deleteAccount']);

Route::post('back/contacts/index', [ContactController::class, 'index']);
Route::put('back/contacts/{id}/{column}', [ContactController::class, 'toggle']);
Route::post('/contacts', [ContactController::class, 'store']);

Route::get('/reports/monthly', [AdminController::class, 'monthlyReport']);


Route::middleware('auth:sanctum')->get('/user/index-user', [OrderController::class, 'indexUser']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user-products', [UserProductController::class, 'store']);
    Route::get('/user-products', [UserProductController::class, 'index']);
});
Route::post('send-otp', [UserController::class, 'sendOtp']);
Route::post('/verify-otp', [UserController::class, 'verifyOtp']);
Route::post('reset-password', [UserController::class, 'resetPassword']);






    Route::post('back/coupon/index', [CouponController::class, 'index']);
    Route::post('back/coupon/restore', [CouponController::class, 'restore']);
    Route::delete('back/coupon/delete', [CouponController::class, 'destroy']);
    Route::delete('back/coupon/force-delete', [CouponController::class, 'forceDelete']);
    Route::put('back/coupon/{id}/{column}', [CouponController::class, 'toggle']);
    Route::post('back/coupon/{coupon}/update', [CouponController::class, 'update']);
    Route::get('coupons/search', [CouponController::class, 'searchByCode']);
    Route::apiResource('back/coupon', CouponController::class);



    Route::post('back/setting/index', [SettingController::class, 'index']);
    Route::post('back/setting/restore', [SettingController::class, 'restore']);
    Route::delete('back/setting/delete', [SettingController::class, 'destroy']);
    Route::delete('back/setting/force-delete', [SettingController::class, 'forceDelete']);
    Route::put('back/setting/{id}/{column}', [SettingController::class, 'toggle']);
    Route::post('back/setting/{setting}/update', [SettingController::class, 'update']);
    Route::get('public-setting', [SettingController::class, 'publicSetting']);
    Route::apiResource('back/setting', SettingController::class);



    Route::get('/tap/callback', [UserController::class, 'callback'])->name('tap.callback');
