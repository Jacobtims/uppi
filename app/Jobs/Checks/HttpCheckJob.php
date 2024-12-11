<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use Illuminate\Support\Facades\Http;

class HttpCheckJob extends CheckJob
{
    protected function performCheck(): array
    {
        $response = Http::timeout(5)->withUserAgent(config('app.name'))
            ->get($this->monitor->address);

        return [
            'status' => $response->successful() ? Status::OK : Status::FAIL,
            'response_code' => $response->status(),
            'output' => $response->body(),
        ];
    }
}
