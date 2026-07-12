<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProductRequest;
use App\Http\Resources\UserProductResource;
use App\Models\Card;
use App\Models\UserProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProductController extends Controller
{
    public function store(UserProductRequest $request)
    {
        // Check that product_number exists in cards
        $card = Card::where('product_number', $request->product_number)->first();

        if (!$card) {
            return response()->json([
                'status' => false,
                'message' => 'رقم المنتج غير موجود في النظام'
            ], 422);
        }

        // Price must not exceed card price
        if ($request->price > $card->price) {
            return response()->json([
                'status' => false,
                'message' => 'السعر لا يمكن أن يتجاوز السعر الأصلي للمنتج'
            ], 422);
        }

        // Upload image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('user_products', 'public');
        }

        // App fee 5%
        $appFee = $request->price * 0.05;
        $finalPrice = $request->price - $appFee;

        $product = UserProduct::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'product_number' => $request->product_number,
            'original_price' => $card->price,
            'app_fee' => $appFee,
            'final_price' => $finalPrice,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل المنتج للبيع',
            'data' => new UserProductResource($product),
        ]);
    }

    public function index()
    {
        $products = UserProduct::with('user')->latest()->paginate(20);
        return UserProductResource::collection($products);
    }
}
