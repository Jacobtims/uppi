<?php

namespace Database\Seeders;

use App\Enums\AlertTriggerType;
use App\Enums\Checks\Status;
use App\Enums\Monitors\MonitorType;
use App\Enums\Types\AlertType;
use App\Models\Alert;
use App\Models\AlertTrigger;
use App\Models\Anomaly;
use App\Models\Check;
use App\Models\Monitor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users
        $janyk = User::factory()->create([
            'name' => 'Janyk Steenbeek',
            'email' => 'janyk@webmethod.nl',
            'is_admin' => true,
        ]);

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
        ]);

        // Create various monitors
        $monitors = [
            Monitor::create([
                'name' => 'Google',
                'type' => MonitorType::HTTP,
                'address' => 'google.com',
                'is_enabled' => true,
                'interval' => 1,
                'consecutive_threshold' => 3,
                'user_id' => $janyk->id,
            ]),
            Monitor::create([
                'name' => 'Nonexistant',
                'type' => MonitorType::HTTP,
                'address' => 'hello.nonexistant',
                'is_enabled' => true,
                'interval' => 1,
                'consecutive_threshold' => 2,
                'user_id' => $janyk->id,
            ]),
            Monitor::create([
                'name' => 'Google DNS',
                'type' => MonitorType::TCP,
                'address' => '8.8.8.8',
                'port' => 53,
                'is_enabled' => true,
                'interval' => 5,
                'consecutive_threshold' => 3,
                'user_id' => $janyk->id,
            ]),
            Monitor::create([
                'name' => 'Test Monitor',
                'type' => MonitorType::HTTP,
                'address' => 'test.com',
                'is_enabled' => true,
                'interval' => 1,
                'consecutive_threshold' => 2,
                'user_id' => $testUser->id,
            ]),
        ];

        // Create different types of alerts
        $alerts = [
            Alert::create([
                'name' => 'Email Alert',
                'destination' => 'webmaster@localhost',
                'type' => AlertType::EMAIL,
                'is_enabled' => true,
                'user_id' => $janyk->id,
            ]),
            Alert::create([
                'name' => 'Slack Alert',
                'destination' => '#monitoring',
                'type' => AlertType::SLACK,
                'is_enabled' => true,
                'config' => ['slack_token' => 'xoxb-test-token'],
                'user_id' => $janyk->id,
            ]),
            Alert::create([
                'name' => 'SMS Alert',
                'destination' => '+31612345678',
                'type' => AlertType::BIRD,
                'is_enabled' => true,
                'config' => [
                    'bird_api_key' => 'test-key',
                    'bird_originator' => 'UPTIME',
                ],
                'user_id' => $janyk->id,
            ]),
            Alert::create([
                'name' => 'Test Alert',
                'destination' => 'test@test.com',
                'type' => AlertType::EMAIL,
                'is_enabled' => true,
                'user_id' => $testUser->id,
            ]),
        ];

        // Attach alerts to monitors
        foreach ($monitors as $monitor) {
            // Attach all alerts belonging to the monitor's user
            $monitor->alerts()->attach(
                Alert::where('user_id', $monitor->user_id)->pluck('id')
            );
        }

        // Create some standalone test anomalies and triggers
        $this->createTestAnomalies($monitors[0], $alerts[0]); // Google with Email
        $this->createTestAnomalies($monitors[1], $alerts[1]); // Nonexistant with Slack
        $this->createTestAnomalies($monitors[2], $alerts[2]); // DNS with SMS

        $now = now();

        // Generate historical data for each monitor
        foreach ($monitors as $monitor) {
            $lastStatus = Status::OK;
            $activeAnomaly = null;

            // Generate data for last 14 days
            for ($i = 13; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);

                // Generate checks for each hour
                for ($hour = 0; $hour < 24; $hour++) {
                    $checkTime = $date->copy()->hour($hour);

                    // Determine check status based on scenarios
                    $status = $this->determineCheckStatus($monitor, $lastStatus, $hour);
                    $lastStatus = $status;

                    // Create the check
                    $check = Check::create([
                        'monitor_id' => $monitor->id,
                        'status' => $status,
                        'response_time' => $status === Status::OK ? rand(50, 500) : null,
                        'response_code' => $status === Status::OK ? 200 : 500,
                        'output' => $status === Status::OK ? 'Success' : 'Failed to connect',
                        'checked_at' => $checkTime,
                        'created_at' => $checkTime,
                        'updated_at' => $checkTime,
                    ]);

                    // Handle anomaly creation and closure
                    if ($status === Status::FAIL && !$activeAnomaly) {
                        $activeAnomaly = $this->createAnomaly($monitor, $check, $checkTime);
                        $check->anomaly()->associate($activeAnomaly);
                        $check->save();

                        // Create alert triggers
                        $this->createAlertTriggers($monitor, $activeAnomaly, AlertTriggerType::DOWN, $check);
                    } elseif ($status === Status::OK && $activeAnomaly) {
                        $activeAnomaly->ended_at = $checkTime;
                        $activeAnomaly->save();

                        $check->anomaly()->associate($activeAnomaly);
                        $check->save();

                        // Create recovery triggers
                        $this->createAlertTriggers($monitor, $activeAnomaly, AlertTriggerType::RECOVERY, $check);

                        $activeAnomaly = null;
                    } elseif ($activeAnomaly) {
                        $check->anomaly()->associate($activeAnomaly);
                        $check->save();
                    }
                }
            }
        }
    }

    protected function createTestAnomalies(Monitor $monitor, Alert $alert): void
    {
        $now = now();

        // Create a very short anomaly (5 minutes)
        $shortAnomaly = Anomaly::create([
            'monitor_id' => $monitor->id,
            'started_at' => $now->copy()->subHours(2),
            'ended_at' => $now->copy()->subHours(2)->addMinutes(5),
        ]);

        // Create a medium anomaly (2 hours)
        $mediumAnomaly = Anomaly::create([
            'monitor_id' => $monitor->id,
            'started_at' => $now->copy()->subDays(2),
            'ended_at' => $now->copy()->subDays(2)->addHours(2),
        ]);

        // Create a long anomaly (1 day)
        $longAnomaly = Anomaly::create([
            'monitor_id' => $monitor->id,
            'started_at' => $now->copy()->subDays(5),
            'ended_at' => $now->copy()->subDays(4),
        ]);

        // Create an ongoing anomaly
        $ongoingAnomaly = Anomaly::create([
            'monitor_id' => $monitor->id,
            'started_at' => $now->copy()->subHours(1),
        ]);

        // Create triggers for each anomaly
        foreach ([$shortAnomaly, $mediumAnomaly, $longAnomaly, $ongoingAnomaly] as $anomaly) {
            // Create down trigger
            AlertTrigger::create([
                'anomaly_id' => $anomaly->id,
                'alert_id' => $alert->id,
                'monitor_id' => $monitor->id,
                'type' => AlertTriggerType::DOWN,
                'channels_notified' => [$alert->type],
                'metadata' => [
                    'monitor_name' => $monitor->name,
                    'monitor_target' => $monitor->address,
                ],
                'triggered_at' => now(),
            ]);

            // Create recovery trigger if anomaly is closed
            if ($anomaly->ended_at) {
                AlertTrigger::create([
                    'anomaly_id' => $anomaly->id,
                    'alert_id' => $alert->id,
                    'monitor_id' => $monitor->id,
                    'type' => AlertTriggerType::RECOVERY,
                    'channels_notified' => [$alert->type],
                    'metadata' => [
                        'monitor_name' => $monitor->name,
                        'monitor_target' => $monitor->address,
                        'started_at' => $anomaly->started_at->format('Y-m-d H:i:s'),
                        'ended_at' => $anomaly->ended_at->format('Y-m-d H:i:s'),
                        'downtime_duration' => $anomaly->started_at->diffForHumans($anomaly->ended_at, true),
                    ],
                    'triggered_at' => now(),
                ]);
            }
        }
    }

    protected function determineCheckStatus(Monitor $monitor, Status $lastStatus, int $hour): Status
    {
        // Create patterns of failures
        if (str_contains($monitor->address, 'nonexistant')) {
            // Frequently failing monitor
            return rand(0, 4) > 0 ? Status::FAIL : Status::OK;
        } elseif ($hour >= 2 && $hour <= 4) {
            // Create maintenance window failures between 2 AM and 4 AM
            return rand(0, 2) > 0 ? Status::FAIL : Status::OK;
        } elseif ($lastStatus === Status::FAIL) {
            // 70% chance to continue failing if already failing
            return rand(0, 100) < 70 ? Status::FAIL : Status::OK;
        }

        // 95% uptime for other cases
        return rand(0, 100) < 95 ? Status::OK : Status::FAIL;
    }

    protected function createAnomaly(Monitor $monitor, Check $check, Carbon $startTime): Anomaly
    {
        return Anomaly::create([
            'monitor_id' => $monitor->id,
            'started_at' => $startTime,
            'created_at' => $startTime,
            'updated_at' => $startTime,
        ]);
    }

    protected function createAlertTriggers(Monitor $monitor, Anomaly $anomaly, AlertTriggerType $type, ?Check $check = null): void
    {
        foreach ($monitor->alerts as $alert) {
            $metadata = [
                'monitor_name' => $monitor->name,
                'monitor_target' => $monitor->address,
            ];

            if ($type === AlertTriggerType::RECOVERY) {
                $metadata['started_at'] = $anomaly->started_at->format('Y-m-d H:i:s');
                $metadata['ended_at'] = $anomaly->ended_at->format('Y-m-d H:i:s');
                $metadata['downtime_duration'] = $anomaly->started_at->diffForHumans($anomaly->ended_at, true);
            }

            AlertTrigger::create([
                'anomaly_id' => $anomaly->id,
                'alert_id' => $alert->id,
                'monitor_id' => $monitor->id,
                'type' => $type,
                'channels_notified' => [$alert->type],
                'metadata' => $metadata,
                'triggered_at' => now(),
            ]);
        }
    }
}
