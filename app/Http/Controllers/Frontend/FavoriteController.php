<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

use App\Models\Favorite;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\JsonResponse;
use App\Http\Resources\FavoriteResource;

class FavoriteController extends BaseController
{
    // Add or remove from favorites
    public function handleFavorite(Request $request)
    {
        try {
            $user = Auth::user();
            $card = Card::findOrFail($request->card_id);

            switch ($request->method) {
                case 'add':
                    Favorite::create([
                        'user_id' => $user->id,
                        'card_id' => $card->id,
                    ]);
                    return JsonResponse::respondSuccess('Card added to favorites');

                case 'delete':
                    $favorite = Favorite::where('user_id', $user->id)
                        ->where('card_id', $card->id)
                        ->first();
                    if ($favorite) {
                        $favorite->delete();
                        return JsonResponse::respondSuccess('Card removed from favorites');
                    }
                    return JsonResponse::respondError('Card not found in favorites');

                default:
                    return JsonResponse::respondError('Invalid method');
            }
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    // Get all favorites
    public function getFavorites()
    {
        try {
            $user = Auth::user();
            $favorites = Favorite::where('user_id', $user->id)->with('card')->get();

            return JsonResponse::respondSuccess('Favorites retrieved successfully', FavoriteResource::collection($favorites));
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}
