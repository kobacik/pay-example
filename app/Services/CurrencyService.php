<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{

    public function convertToBase($currency, $amount)
    {
        $rates = Http::get(config('pay-example.exchange-api-url'));
        $exchange = $rates['rates'][$currency];
        return $amount / $exchange;
    }

}
