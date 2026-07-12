<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;
use App\Helpers\JsonResponse;
use App\Http\Requests\CardRequest;
use App\Http\Requests\CardUpdateRequest;
use App\Http\Resources\CardResource;
use App\Interfaces\CardRepositoryInterface;
use App\Models\Card;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Str;
use Illuminate\Validation\ValidationException;

class CardController extends  BaseController
{
    protected mixed $crudRepository;

    public function __construct(CardRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $cards = CardResource::collection($this->crudRepository->all(
                ['category', 'brand'],
                [],
                ['*']
            ));
            return $cards->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(CardRequest $request)
    {
        try {
            $data = $request->validated();
            $slugOptions = getSlugOptions();
            $data['slug'] = Str::slug($data['name'], $slugOptions->slugSeparator, $slugOptions->slugLanguage);
            $data['admin_id'] = auth('admin')->id();
            $data['product_number'] = 'PO' . date('Ymd') . mt_rand(1000, 9999);

            if (!empty($data['old_price'])) {
                if (!empty($data['discount'])) {
                    $discountValue = (float)$data['discount'];
                    $oldPrice = (float)$data['old_price'];

                    $data['price'] = $oldPrice - ($oldPrice * $discountValue / 100);
                } else {
                    $data['price'] = $data['old_price'];
                }
            }

            $model = $this->crudRepository->create($data);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('cards', 'public');
                $model->update(['image' => asset('storage/' . $path)]);
            }

            if ($request->hasFile('gallery')) {
                $galleryPaths = [];
                foreach ($request->file('gallery') as $file) {
                    $path = $file->store('cards/gallery', 'public');
                    $galleryPaths[] = asset('storage/' . $path);
                }
                $model->update(['gallery' => $galleryPaths]);
            }
            return JsonResponse::respondSuccess('Product Create successfully');

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function show(Card $card)
    {
        try {
            $card->load(['category', 'brand']);
            return JsonResponse::respondSuccess('Card fetched successfully', new CardResource($card));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(CardUpdateRequest $request, Card $card)
    {
        try {
            $data = $request->validated();

            // حساب السعر زي store
            if (!empty($data['old_price'])) {
                if (!empty($data['discount'])) {
                    $discountValue = (float) $data['discount'];
                    $oldPrice = (float) $data['old_price'];

                    $data['price'] = $oldPrice - ($oldPrice * $discountValue / 100);
                } else {
                    $data['price'] = $data['old_price'];
                }
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('cards', 'public');
                $data['image'] = asset('storage/' . $path);
            }

            if ($request->hasFile('gallery')) {
                $galleryPaths = [];
                foreach ($request->file('gallery') as $file) {
                    $path = $file->store('cards/gallery', 'public');
                    $galleryPaths[] = asset('storage/' . $path);
                }
                $data['gallery'] = $galleryPaths;
            }

            $card->update($data);

            return JsonResponse::respondSuccess('Card updated successfully');
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request)
    {
        try {
            $count = $this->crudRepository->deleteRecords('cards', $request['ids']);
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
            $this->crudRepository->restoreItem(Card::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request)
    {
        try {
            $this->crudRepository->deleteRecordsFinial(Card::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function publicCards(): \Illuminate\Http\JsonResponse
    {
        try {
            $cards = Card::with(['category', 'brand'])
                ->where('active', true)
                ->get();
            return JsonResponse::respondSuccess('Cards fetched successfully', CardResource::collection($cards));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function usersWithCards()
    {
        $users = User::with('cards')->get();

        return JsonResponse::respondSuccess('Users with their cards', $users);
    }
}
