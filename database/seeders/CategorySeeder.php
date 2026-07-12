<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_en' => 'App Stores',
                'name_ar' => 'متاجر التطبيقات',
                'icon' => 'solar:card-bold',
            ],
            [
                'name_en' => 'Game Cards',
                'name_ar' => 'بطاقات الألعاب',
                'icon' => 'solar:gamepad-bold',
            ],
            [
                'name_en' => 'Online Shopping',
                'name_ar' => 'تسوق عبر الإنترنت',
                'icon' => 'solar:bag-4-bold',
            ],
            [
                'name_en' => 'Recharge Cards',
                'name_ar' => 'بطاقات الشحن',
                'icon' => 'solar:smartphone-2-bold',
            ],
            [
                'name_en' => 'Services',
                'name_ar' => 'خدمات',
                'icon' => 'solar:settings-bold',
            ],
            [
                'name_en' => 'Music',
                'name_ar' => 'موسيقى',
                'icon' => 'solar:music-note-bold',
            ],
            [
                'name_en' => 'Video',
                'name_ar' => 'فيديو',
                'icon' => 'solar:video-library-bold',
            ],
            [
                'name_en' => 'LikeCard',
                'name_ar' => 'لايك كارد',
                'icon' => 'solar:heart-bold',
            ],
            [
                'name_en' => 'Travel & Experiences',
                'name_ar' => 'السفر والتجارب',
                'icon' => 'solar:plain-2-bold',
            ],
            [
                'name_en' => 'Game Offers',
                'name_ar' => 'عروض الألعاب',
                'icon' => 'solar:gamepad-charge-bold',
            ],
            [
                'name_en' => 'Flash Sale',
                'name_ar' => 'فلاش سيل',
                'icon' => 'solar:sale-bold',
            ],
            [
                'name_en' => 'New from LikeCard',
                'name_ar' => 'الجديد من لايك كارد',
                'icon' => 'solar:star-bold',
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['slug' => Str::slug($cat['name_en'])],
                [
                    'name' => $cat['name_en'],
                    'slug' => Str::slug($cat['name_en']),
                    'name_ar' => $cat['name_ar'],
                    'slug_ar' => Str::slug($cat['name_ar']),
                    'icon' => $cat['icon'],
                    'active' => true,
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
