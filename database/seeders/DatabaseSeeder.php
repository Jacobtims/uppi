<?php

namespace Database\Seeders;

use App\Enums\Checks\Status;
use App\Enums\Monitors\MonitorType;
use App\Enums\Types\AlertType;
use App\Models\Alert;
use App\Models\Anomaly;
use App\Models\Check;
use App\Models\Monitor;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user =User::factory()->create([
            'name' => 'Janyk Steenbeek',
            'email' => 'janyk@webmethod.nl',
        ]);

       $user2 = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
        ]);


        Monitor::create([
            'name' => 'Google',
            'type' => MonitorType::HTTP,
            'address' => 'google.com',
            'is_enabled' => true,
            'interval' => 1,
            'user_id' => $user2->id,
        ]);

        Monitor::create([
            'name' => 'Nonexistant',
            'type' => MonitorType::HTTP,
            'address' => 'hello.nonexistant',
            'is_enabled' => true,
            'interval' => 1,
            'user_id' => $user->id,
        ]);

        Monitor::create([
            'name' => 'Google DNS',
            'type' => MonitorType::TCP,
            'address' => '8.8.8.8',
            'port' => 53,
            'is_enabled' => true,
            'interval' => 5,
            'user_id' => $user->id,
        ]);

        $alert = Alert::create([
            'name' => 'Webmaster',
            'destination' => 'webmaster@localhost',
            'type' => AlertType::EMAIL,
            'is_enabled' => true,
        ]);

        $alert->monitors()->attach(Monitor::all());

        $monitors = Monitor::all();
        $now = now();

        foreach ($monitors as $monitor) {
            // Generate checks for last 14 days
            for ($i = 0; $i < 14; $i++) {
                $date = $now->copy()->subDays($i);

                // Generate checks for each hour
                for ($hour = 0; $hour < 24; $hour++) {
                    $checkTime = $date->copy()->hour($hour);

                    // For the nonexistent domain, simulate some failures
                    $isSuccess = !str_contains($monitor->address, 'nonexistant') || rand(0, 4) > 0;

                    Check::create([
                        'monitor_id' => $monitor->id,
                        'status' => $isSuccess ? Status::OK : Status::FAIL,
                        'response_time' => $isSuccess ? rand(50, 500) : null,
                        'checked_at' => $checkTime,
                        'created_at' => $checkTime,
                        'updated_at' => $checkTime,
                    ]);

                    if (!$isSuccess) {
                        $anomaly = new Anomaly([
                            'started_at' => $checkTime,
                            'monitor_id' => $monitor->id,
                            'alert_id' => $alert->id,
                        ]);
                        $anomaly->save();
                    }
                }
            }
        }
    }
}
