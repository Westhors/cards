<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\CountryShipping;

class CountriesTableSeeder extends Seeder
{
    public function run(): void
    {
        // قائمة البلدان الأوروبية (شائعة التفسير) مع أسعار شحن مقترحة (EUR)
        $countries = [
            'Albania' => 18.00,
            'Andorra' => 8.00,
            'Austria' => 10.00,
            'Belarus' => 15.00,
            'Belgium' => 10.00,
            'Bosnia and Herzegovina' => 18.00,
            'Bulgaria' => 15.00,
            'Croatia' => 15.00,
            'Cyprus' => 22.00,
            'Czech Republic' => 12.00,
            'Denmark' => 12.00,
            'Estonia' => 14.00,
            'Finland' => 12.00,
            'France' => 10.00,
            'Germany' => 10.00,
            'Greece' => 18.00,
            'Hungary' => 12.00,
            'Iceland' => 25.00,
            'Ireland' => 12.00,
            'Italy' => 10.00,
            'Kosovo' => 18.00,
            'Latvia' => 14.00,
            'Liechtenstein' => 8.00,
            'Lithuania' => 14.00,
            'Luxembourg' => 10.00,
            'Malta' => 22.00,
            'Moldova' => 18.00,
            'Monaco' => 8.00,
            'Montenegro' => 18.00,
            'Netherlands' => 10.00,
            'North Macedonia' => 18.00,
            'Norway' => 12.00,
            'Poland' => 12.00,
            'Portugal' => 12.00,
            'Romania' => 15.00,
            'San Marino' => 8.00,
            'Serbia' => 18.00,
            'Slovakia' => 12.00,
            'Slovenia' => 12.00,
            'Spain' => 10.00,
            'Sweden' => 12.00,
            'Switzerland' => 12.00,
            'Ukraine' => 20.00,
            'United Kingdom' => 15.00,
            'Vatican City' => 8.00,
        ];

        foreach ($countries as $name => $price) {
            CountryShipping::updateOrCreate(
                ['name' => $name],
                [
                    'iso_code' => null,
                    'shipping_price' => $price,
                    'currency' => 'EUR'
                ]
            );
        }
    }
}
