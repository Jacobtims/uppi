<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use Illuminate\Support\Facades\Http;

class HttpCheckJob extends CheckJob
{
    protected function performCheck(): array
    {
        $response = Http::timeout(5)->withUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36
' . config('app.name'))
            ->get($this->monitor->address);

        return [
            'status' => $response->successful() ? Status::OK : Status::FAIL,
            'response_code' => $response->status(),
            'output' => json_encode([
                'headers' => $response->headers(),
                'reason' => $response->reason(),
            ], JSON_PRETTY_PRINT),
        ];
    }
}
