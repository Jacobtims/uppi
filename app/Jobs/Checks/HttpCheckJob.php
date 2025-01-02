<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class HttpCheckJob extends CheckJob
{
    protected function performCheck(): array
    {
        try {
            $response = Http::timeout(5)
                ->withUserAgent($this->monitor->user_agent ?? config('app.name'))
                ->get($this->monitor->address);
        } catch (ConnectionException $exception) {
            return [
                'status' => Status::FAIL,
                'response_code' => $exception,
                'output' => json_encode([
                    'error' => $exception->getMessage(),
                ]),
            ];
        }

        return [
            'status' => $response->successful() ? Status::OK : Status::FAIL,
            'response_code' => $response->status(),
            'output' => json_encode([
                'headers' => $response->headers(),
                'reason' => $response->reason(),
            ]),
        ];
    }
}
