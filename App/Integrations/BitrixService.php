<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BitrixService
{
    public function createDeal(array $params)
    {
        return Cache::remember("bitrix_deal_{$params['client_id']}", 3600, function() use ($params) {
            return 'BX_' . uniqid();
        });
    }
}