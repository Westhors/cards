<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

use App\Helpers\JsonResponse;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryWithProductResource;
use App\Http\Resources\OfferResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use App\Models\Offer;
use Exception;
use Illuminate\Http\Request;

class OfferController extends BaseController
{


    public function latestOffer(): \Illuminate\Http\JsonResponse
    {
        try {
            $offer = Offer::with(['card' => function ($query) {
                    $query->where('active', true);
                }])
                ->orderBy('id', 'desc')
                ->first();

            if (!$offer) {
                return JsonResponse::respondError('No offers found');
            }

            return JsonResponse::respondSuccess('Offer fetched successfully', new OfferResource($offer));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function offers(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $offers = Offer::with('card')->where('active', true)
                ->orderBy('id', 'desc')
                ->paginate($perPage);
            return JsonResponse::respondSuccess('offers fetched successfully', OfferResource::collection($offers));
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}
