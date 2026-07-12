<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على فئة "Video Streaming"
        $parentCategory = Category::where('slug', 'video-streaming')->first();

        if (!$parentCategory) {
            $parentCategory = Category::create([
                'name' => 'Video Streaming',
                'slug' => 'video-streaming',
                'name_ar' => 'بث الفيديو',
                'slug_ar' => 'بث-الفيديو',
                'icon' => 'solar:video-library-bold',
                'active' => true,
                'parent_id' => null,
            ]);
        }

        // قائمة العلامات التجارية
        $brands = [
            [
                'name' => 'Netflix',
                'slug' => Str::slug('Netflix'),
                'name_ar' => 'نيتفليكس',
                'slug_ar' => Str::slug('نيتفليكس'),
                'icon' => 'https://cdn.likecard.com/netflix-icon.png',
            ],
            [
                'name' => 'Shahid',
                'slug' => Str::slug('Shahid'),
                'name_ar' => 'شاهد',
                'slug_ar' => Str::slug('شاهد'),
                'icon' => 'https://cdn.likecard.com/shahid-icon.png',
            ],
            [
                'name' => 'OSN',
                'slug' => Str::slug('OSN'),
                'name_ar' => 'أو إس إن',
                'slug_ar' => Str::slug('أو-إس-إن'),
                'icon' => 'https://cdn.likecard.com/osn-icon.png',
            ],
            [
                'name' => 'STARZPLAY',
                'slug' => Str::slug('STARZPLAY'),
                'name_ar' => 'ستارزبلاي',
                'slug_ar' => Str::slug('ستارزبلاي'),
                'icon' => 'https://cdn.likecard.com/starzplay-icon.png',
            ],
            [
                'name' => 'Weyyak',
                'slug' => Str::slug('Weyyak'),
                'name_ar' => 'وياك',
                'slug_ar' => Str::slug('وياك'),
                'icon' => 'https://cdn.likecard.com/weyyak-icon.png',
            ],
            [
                'name' => 'Viu',
                'slug' => Str::slug('Viu'),
                'name_ar' => 'فيو',
                'slug_ar' => Str::slug('فيو'),
                'icon' => 'https://cdn.likecard.com/viu-icon.png',
            ],
            [
                'name' => 'Smashi.TV',
                'slug' => Str::slug('Smashi.TV'),
                'name_ar' => 'سماشي',
                'slug_ar' => Str::slug('سماشي'),
                'icon' => 'https://cdn.likecard.com/smashi-icon.png',
            ],
            [
                'name' => 'STC TV',
                'slug' => Str::slug('STC TV'),
                'name_ar' => 'إس تي سي تي في',
                'slug_ar' => Str::slug('إس-تي-سي-تي-في'),
                'icon' => 'https://cdn.likecard.com/stctv-icon.png',
            ],
            [
                'name' => 'Twitch',
                'slug' => Str::slug('Twitch'),
                'name_ar' => 'تويتش',
                'slug_ar' => Str::slug('تويتش'),
                'icon' => 'https://cdn.likecard.com/twitch-icon.png',
            ],
            [
                'name' => 'Spacetoon Go',
                'slug' => Str::slug('Spacetoon Go'),
                'name_ar' => 'سبيستون جو',
                'slug_ar' => Str::slug('سبيستون-جو'),
                'icon' => 'https://cdn.likecard.com/spacetoon-icon.png',
            ],
            [
                'name' => 'Yango Play',
                'slug' => Str::slug('Yango Play'),
                'name_ar' => 'يانغو بلاي',
                'slug_ar' => Str::slug('يانغو-بلاي'),
                'icon' => 'https://cdn.likecard.com/yango-icon.png',
            ],
        ];

        foreach ($brands as $brand) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $brand['slug']],
                array_merge($brand, [
                    'active' => true,
                    'parent_id' => $parentCategory->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
