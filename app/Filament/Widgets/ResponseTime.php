<?php

namespace App\Filament\Widgets;

use App\Models\Check;
use App\Models\Monitor;
use Filament\Widgets\ChartWidget;

class ResponseTime extends ChartWidget
{
    protected static ?string $heading = 'Performance';

    protected int|string|array $columnSpan = [
        'sm' => 12,
        'md' => 12,
        'lg' => 7,
    ];

    protected int $refreshInterval = 60;

    protected function getMaxHeight(): string
    {
        return '200px';
    }

    protected function getData(): array
    {
        $checks = Check::query()
            ->selectRaw('monitor_id, DATE_FORMAT(checked_at, "%d-%m %H") as hour, AVG(response_time) as avg_response_time')
            ->where('checked_at', '>=', now()->subDays(7))
            ->whereRaw('HOUR(checked_at) % 12 = 0')
            ->whereHas('monitor', function ($query) {
                $query->where('is_enabled', true);
            })
            ->groupBy(['monitor_id', 'hour'])
            ->with('monitor:id,name')
            ->get();

        $monitorData = $checks->groupBy('monitor_id');
        $datasets = [];

        foreach ($monitorData as $monitorId => $monitorChecks) {
            $monitor = $monitorChecks->first()?->monitor;
            if (!$monitor) continue;

            $color = self::generatePastelColorBasedOnMonitorId($monitorId);

            $datasets[] = [
                'label' => $monitor->name,
                'data' => $monitorChecks->pluck('avg_response_time')->toArray(),
                'type' => 'line',
                'backgroundColor' => $color,
                'borderColor' => $color,
                'pointBackgroundColor' => $color,
                'pointBorderColor' => $color,
                'fill' => false,
            ];
        }

        $firstMonitorData = $monitorData->first();
        return [
            'labels' => $firstMonitorData ? $firstMonitorData->pluck('hour')->toArray() : [],
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
            $r = $c;
            $g = $x;
            $b = 0;
        } elseif ($hue < 120) {
            $r = $x;
            $g = $c;
            $b = 0;
        } elseif ($hue < 180) {
            $r = 0;
            $g = $c;
            $b = $x;
        } elseif ($hue < 240) {
            $r = 0;
            $g = $x;
            $b = $c;
        } elseif ($hue < 300) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
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
