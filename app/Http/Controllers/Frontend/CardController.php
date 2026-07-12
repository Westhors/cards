<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

use App\Helpers\JsonResponse;
use App\Http\Requests\CardRequest;
use App\Http\Resources\CardResource;
use App\Interfaces\CardRepositoryInterface;
use App\Models\Card;
use App\Models\Category;
use App\Models\User;
use App\Services\TapPaymentService;
use Exception;
use Illuminate\Http\Request;

class CardController extends  BaseController
{
    protected mixed $crudRepository;

    public function __construct(CardRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $query = Card::with(['category', 'brand'])
                ->where('active', true);

            if ($request->has('sort')) {
                switch ($request->sort) {
                    case 'highest_price':
                        $query->orderBy('price', 'asc');
                        break;
                    case 'lowest_price':
                        $query->orderBy('price', 'desc');
                        break;
                    case 'latest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'most_popular':
                        $query->orderBy('name', 'asc');
                        break;
                }
            }

            $cards = $query->get();

            return JsonResponse::respondSuccess('Cards fetched successfully', CardResource::collection($cards));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $card = Card::with(['category', 'brand', 'reviews.user'])
                ->where('active', true)
                ->where('id', $id)
                ->first();

            return JsonResponse::respondSuccess('Card fetched successfully', new CardResource($card));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage(), 404);
        }
    }



    public function searchByName(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $query = Card::query();

            // بحث بالاسم (keyword)
            if ($request->filled('keyword')) {
                $query->where('name', 'LIKE', '%' . $request->keyword . '%');
            }

            // بحث حسب الـ category_id
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // بحث بالحد الأدنى للسعر
            if ($request->filled('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }

            // بحث بالحد الأقصى للسعر
            if ($request->filled('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }

            // بحث حسب التوفر في المخزون (in_stock)
            if ($request->filled('in_stock')) {
                if ($request->in_stock == true) {
                    $query->where('quantity', '>', 0); // متوفرة
                } elseif ($request->in_stock == false) {
                    $query->where('quantity', '<=', 0); // غير متوفرة
                }
            }

            // شرط الكروت المفعلة
            $query->where('active', true);

            $cards = $query->get();

            return JsonResponse::respondSuccess('Cards fetched successfully', CardResource::collection($cards));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function getByBrandSlug($slug)
    {
        try {
            $brand = Category::where('slug', $slug)->first();
            if (!$brand) {
                return JsonResponse::respondError('Brand not found.', 404);
            }
            $cards = $brand->brandCards;
            return JsonResponse::respondSuccess('Cards fetched successfully', CardResource::collection($cards));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function addReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);
        $card = Card::findOrFail($id);

        $review = $card->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return JsonResponse::respondSuccess('Review added successfully', $review);
    }


    public function pay(TapPaymentService $tap)
    {
        $customer = [
            'first_name' => 'Ahmed',
            'last_name' => 'Abdullah',
            'email' => 'test@email.com',
            'phone' => '50000000',
        ];

        $charge = $tap->createCharge(100, $customer, 123);

        // هذا رابط الدفع الذي سترسله للمستخدم
        return redirect($charge['transaction']['url']);
    }
}
