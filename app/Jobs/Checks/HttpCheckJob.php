<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
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
                'output' => json_encode([
                    'type' => 'connection_exception',
                    'error' => $exception->getMessage(),
                ]),
            ];
        } catch (RequestException $exception) {
            return [
                'status' => Status::FAIL,
                'output' => json_encode([
                    'type' => 'request_exception',
                    'error' => $exception->getMessage(),
                ]),
            ];
        } catch (GuzzleException $exception) {
            return [
                'status' => Status::FAIL,
                'output' => json_encode([
                    'type' => 'unknown_failure',
                    'error' => $exception->getMessage(),
                ]),
            ];
        }

        return [
            'status' => $response->successful() ? Status::OK : Status::FAIL,
            'response_code' => $response->status(),
            'output' => json_encode([
                'reason' => $response->reason(),
            ]),
        ];
    }
}
