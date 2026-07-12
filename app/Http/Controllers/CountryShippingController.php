<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CountryShipping;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CountryShippingController extends Controller
{
public function index(Request $request)
{
    try {
        $filters = $request->input('filters', []);
        $orderBy = $request->input('orderBy', 'id');
        $orderByDirection = $request->input('orderByDirection', 'asc');
        $perPage = $request->input('perPage', 10);
        $paginate = $request->boolean('paginate', true);

        // 🔹 بناء الاستعلام
        $query = CountryShipping::query();

        // ✅ الفلاتر
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['iso_code'])) {
            $query->where('iso_code', 'like', '%' . $filters['iso_code'] . '%');
        }

        if (!empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (!empty($filters['shipping_price_min'])) {
            $query->where('shipping_price', '>=', $filters['shipping_price_min']);
        }

        if (!empty($filters['shipping_price_max'])) {
            $query->where('shipping_price', '<=', $filters['shipping_price_max']);
        }

        // ✅ الترتيب
        $query->orderBy($orderBy, $orderByDirection);

        // ✅ التنفيذ
        if ($paginate) {
            $countries = $query->paginate($perPage);
        } else {
            $countries = $query->get();
        }

        // ✅ تجهيز الريسبونس بنفس الشكل المطلوب
        if ($paginate) {
            return response()->json([
                'data' => $countries->items(),
                'links' => [
                    'first' => $countries->url(1),
                    'last' => $countries->url($countries->lastPage()),
                    'prev' => $countries->previousPageUrl(),
                    'next' => $countries->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $countries->currentPage(),
                    'from' => $countries->firstItem(),
                    'last_page' => $countries->lastPage(),
                    'links' => $countries->linkCollection(),
                    'path' => $countries->path(),
                    'per_page' => $countries->perPage(),
                    'to' => $countries->lastItem(),
                    'total' => $countries->total(),
                ],
                'result' => 'Success',
                'message' => 'Countries fetched successfully',
                'status' => 200,
            ]);
        } else {
            // لو بدون paginate
            return response()->json([
                'data' => $countries,
                'links' => null,
                'meta' => null,
                'result' => 'Success',
                'message' => 'Countries fetched successfully',
                'status' => 200,
            ]);
        }

    } catch (\Exception $e) {
        return response()->json([
            'result' => 'Error',
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}


    public function show($id): JsonResponse
    {
        $country = CountryShipping::find($id);
        if (! $country) {
            return response()->json(['success' => false, 'message' => 'Country not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $country]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|unique:country_shippings,name',
            'iso_code' => 'nullable|string|max:5',
            'shipping_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
        ]);

        $country = CountryShipping::create($data);

        return response()->json(['success' => true, 'data' => $country], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $country = CountryShipping::find($id);
        if (! $country) {
            return response()->json(['success' => false, 'message' => 'Country not found'], 404);
        }

        $data = $request->validate([
            'iso_code' => 'nullable|string|max:5',
            'shipping_price' => 'sometimes|required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
        ]);

        $country->update($data);

        return response()->json(['success' => true, 'data' => $country]);
    }

    public function destroy($id): JsonResponse
    {
        $country = CountryShipping::find($id);
        if (! $country) {
            return response()->json(['success' => false, 'message' => 'Country not found'], 404);
        }
        $country->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
