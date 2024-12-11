<?php

namespace App\Filament\Widgets;

use App\Models\Anomaly;
use App\Models\Check;
use App\Models\Monitor;
use Filament\Widgets\ChartWidget;

class ResponseTime extends ChartWidget
{
    protected static ?string $heading = 'Performance';

    protected int|string|array $columnSpan = 8;

    protected int $refreshInterval = 60;

    protected function getMaxHeight(): string
    {
        return '200px';
    }

    protected function getData(): array
    {
        $monitors = Monitor::all();
        $datasets = [];

        foreach ($monitors as $monitor) {
            $checks = Check::query()
                ->where('monitor_id', $monitor->id)
                ->where('checked_at', '>=', now()->subDays(value: 7))
                ->get()
                ->groupBy(function($check) {
                    return $check->checked_at->format('d-m H').'h';
                })
                ->filter(function($group, $timestamp) {
                    // '01-12 12h'
                    $hour = (int) substr($timestamp, -3, 2);
                    return $hour % 12 === 0;
                })
                ->map(function($group) {
                    return $group->avg('response_time');
                });

                $color = self::generatePastelColorBasedOnMonitorId($monitor->id);

            $datasets[] = [
                'label' => $monitor->name,
                'data' => $checks->values()->toArray(),
                'type' => 'line',
                'backgroundColor' => $color,
                'borderColor' => $color,
                'pointBackgroundColor' => $color,
                'pointBorderColor' => $color,
                'fill' => false,
            ];
        }

        return [
            'labels' => $checks ? $checks->keys()->toArray() : [],
            'datasets' => $datasets,
        ];

    }

    public static function generatePastelColorBasedOnMonitorId(string $monitorId): string
    {
        // string to int
        $monitorId = crc32($monitorId);
        // Use monitor ID as seed for consistent color per monitor
        srand($monitorId);

        // Generate pastel RGB values
        $hue = rand(0, 260);
        $saturation = rand(15, 45); // Lower saturation for pastel
        $lightness = rand(65, 85); // Higher lightness for pastel

        // Convert HSL to RGB
        $c = (1 - abs(2 * ($lightness / 100) - 1)) * ($saturation / 100);
        $x = $c * (1 - abs(fmod($hue / 60, 2) - 1));
        $m = ($lightness / 100) - ($c / 2);

        if ($hue < 60) {
            $r = $c; $g = $x; $b = 0;
        } elseif ($hue < 120) {
            $r = $x; $g = $c; $b = 0;
        } elseif ($hue < 180) {
            $r = 0; $g = $c; $b = $x;
        } elseif ($hue < 240) {
            $r = 0; $g = $x; $b = $c;
        } elseif ($hue < 300) {
            $r = $x; $g = 0; $b = $c;
        } else {
            $r = $c; $g = 0; $b = $x;
        }

        $r = round(($r + $m) * 255);
        $g = round(($g + $m) * 255);
        $b = round(($b + $m) * 255);

        return "rgba($r, $g, $b, 0.8)";
    }

    public function getDescription(): ?string
    {
        return 'Response time per monitor in the last 7 days';
    }

    protected function getType(): string
    {
        return 'line';
    }
}
