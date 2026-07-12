<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TapPaymentService
{
    protected $secret;
    protected $merchantId;

    public function __construct()
    {
        $this->secret = config('services.tap.secret');
        $this->merchantId = config('services.tap.merchant_id');
    }

    public function createCharge($amount, $customer, $orderId)
    {
        $response = Http::withToken($this->secret)
            ->post('https://api.tap.company/v2/charges', [
                "amount" => $amount,
                "currency" => "AED",
                "threeDSecure" => true,
                "save_card" => false,
                "description" => "Order #".$orderId,
                "statement_descriptor" => "My Platform",
                "reference" => [
                    "transaction" => $orderId,
                    "order" => $orderId
                ],
                "customer" => [
                    "first_name" => $customer['first_name'],
                    "last_name" => $customer['last_name'],
                    "email" => $customer['email'],
                    "phone" => [
                        "country_code" => "971",
                        "number" => $customer['phone']
                    ]
                ],
                "source" => [
                    "id" => "src_all"  // لجميع أنواع الدفع في الوضع التجريبي
                ],
                "redirect" => [
                    "url" => route('tap.callback')
                ]
            ]);

        return $response->json();
    }
}
