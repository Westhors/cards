<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;
use App\Models\Cart;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\JsonResponse;

class CartController extends BaseController
{
    // Add to cart or update quantity
    public function handleCart(Request $request)
    {
        try {
            $user = Auth::user();
            $card = Card::findOrFail($request->card_id);

            $color = $request->color; // استقبال اللون

            switch ($request->method) {

                case 'add':
                    Cart::create([
                        'user_id' => $user->id,
                        'card_id' => $card->id,
                        'quantity' => 1,
                        'color' => $color, // ✅ إضافة اللون
                    ]);
                    return JsonResponse::respondSuccess('Card added to cart');

                case 'plus':
                    $cart = Cart::where('user_id', $user->id)
                        ->where('card_id', $card->id)
                        ->where('color', $color) // ✅ البحث باللون
                        ->first();

                    if ($cart) {
                        $cart->quantity += 1;
                        $cart->save();
                        return JsonResponse::respondSuccess('Card quantity increased');
                    }
                    return JsonResponse::respondError('Card not found in cart');

                case 'minus':
                    $cart = Cart::where('user_id', $user->id)
                        ->where('card_id', $card->id)
                        ->where('color', $color) // ✅ البحث باللون
                        ->first();

                    if ($cart) {
                        if ($cart->quantity > 1) {
                            $cart->quantity -= 1;
                            $cart->save();
                            return JsonResponse::respondSuccess('Card quantity decreased');
                        } else {
                            $cart->delete();
                            return JsonResponse::respondSuccess('Card removed from cart');
                        }
                    }

                    return JsonResponse::respondError('Card not found in cart');

                case 'delete':
                    $deleted = Cart::where('user_id', $user->id)
                        ->where('card_id', $card->id)
                        ->where('color', $color) // ✅ البحث باللون
                        ->delete();

                    if ($deleted) {
                        return JsonResponse::respondSuccess('Card removed completely from cart');
                    }
                    return JsonResponse::respondError('Card not found in cart');

                default:
                    return JsonResponse::respondError('Invalid method');
            }
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    // Get all cart items
    public function getCartItems()
    {
        try {
            $user = Auth::user();
            $cartItems = Cart::where('user_id', $user->id)->with('card')->get();
            return JsonResponse::respondSuccess('Cart items retrieved successfully', $cartItems);
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}
