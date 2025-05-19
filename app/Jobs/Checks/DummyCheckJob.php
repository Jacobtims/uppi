<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use App\Models\Monitor;

class DummyCheckJob extends CheckJob
{
    private Status $returnStatus;

    public function __construct(Monitor $monitor, Status $returnStatus = Status::OK)
    {
        parent::__construct($monitor);
        $this->returnStatus = $returnStatus;
    }

    protected function performCheck(): array
    {
        usleep(random_int(100000, 123456));

        return [
            'status' => $this->returnStatus,
            'response_code' => 200,
            'output' => 'test output',
        ];
    }
}
