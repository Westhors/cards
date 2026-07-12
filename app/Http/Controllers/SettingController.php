<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\SettingRequest;
use App\Http\Resources\SettingResource;
use App\Interfaces\SettingRepositoryInterface;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;

class SettingController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(SettingRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $settings = SettingResource::collection(
                $this->crudRepository->all([], [], ['*'])
            );

            return $settings->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function store(SettingRequest $request)
    {
        try {
            $data = $request->validated();

            $setting = $this->crudRepository->create($data);

            // Images Upload
            if ($request->hasFile('promotional_offer_image_one')) {
                $path = $request->file('promotional_offer_image_one')->store('settings', 'public');
                $setting->update(['promotional_offer_image_one' => asset('storage/' . $path)]);
            }

            if ($request->hasFile('promotional_offer_image_two')) {
                $path = $request->file('promotional_offer_image_two')->store('settings', 'public');
                $setting->update(['promotional_offer_image_two' => asset('storage/' . $path)]);
            }

            if ($request->hasFile('promotional_offer_image_three')) {
                $path = $request->file('promotional_offer_image_three')->store('settings', 'public');
                $setting->update(['promotional_offer_image_three' => asset('storage/' . $path)]);
            }

            if ($request->hasFile('promotional_offer_image_four')) {
                $path = $request->file('promotional_offer_image_four')->store('settings', 'public');
                $setting->update(['promotional_offer_image_four' => asset('storage/' . $path)]);
            }

            if ($request->hasFile('promotional_offer_image_five')) {
                $path = $request->file('promotional_offer_image_five')->store('settings', 'public');
                $setting->update(['promotional_offer_image_five' => asset('storage/' . $path)]);
            }

            return JsonResponse::respondSuccess('Setting created successfully');
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function show(Setting $setting)
    {
        try {
            return JsonResponse::respondSuccess(
                'Setting fetched successfully',
                new SettingResource($setting)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(SettingRequest $request, Setting $setting)
    {
        try {
            $data = $request->validated();

            // Update Images
            if ($request->hasFile('promotional_offer_image_one')) {
                $path = $request->file('promotional_offer_image_one')->store('settings', 'public');
                $data['promotional_offer_image_one'] = asset('storage/' . $path);
            }

            if ($request->hasFile('promotional_offer_image_two')) {
                $path = $request->file('promotional_offer_image_two')->store('settings', 'public');
                $data['promotional_offer_image_two'] = asset('storage/' . $path);
            }

            if ($request->hasFile('promotional_offer_image_three')) {
                $path = $request->file('promotional_offer_image_three')->store('settings', 'public');
                $data['promotional_offer_image_three'] = asset('storage/' . $path);
            }

              if ($request->hasFile('promotional_offer_image_four')) {
                $path = $request->file('promotional_offer_image_four')->store('settings', 'public');
                $data['promotional_offer_image_four'] = asset('storage/' . $path);
            }

              if ($request->hasFile('promotional_offer_image_five')) {
                $path = $request->file('promotional_offer_image_five')->store('settings', 'public');
                $data['promotional_offer_image_five'] = asset('storage/' . $path);
            }


            $setting->update($data);

            return JsonResponse::respondSuccess('Setting updated successfully');
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $count = $this->crudRepository->deleteRecords('settings', $request['ids']);

            return $count > 1
                ? JsonResponse::respondError(trans(JsonResponse::MSG_CANNOT_DELETED_MULTI_RESOURCE))
                : JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        try {
            $this->crudRepository->restoreItem(Setting::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request)
    {
        try {
            $this->crudRepository->deleteRecordsFinial(Setting::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function publicSetting(): \Illuminate\Http\JsonResponse
    {
        try {
            $settings = Setting::all();
            return JsonResponse::respondSuccess('Settings fetched successfully', SettingResource::collection($settings));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


}
