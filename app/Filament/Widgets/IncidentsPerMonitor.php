<?php

namespace App\Filament\Widgets;

use App\Models\Anomaly;
use App\Models\Monitor;
use Filament\Widgets\ChartWidget;

class IncidentsPerMonitor extends ChartWidget
{
    protected static ?string $heading = 'Incidents';

    protected int|string|array $columnSpan = [
        'sm' => 12,
        'md' => 12,
        'lg' => 5,
    ];



    protected function getMaxHeight(): string|null
    {
        return '300px';
    }

    protected function getData(): array
    {
        $anomalies = Anomaly::query()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('monitor_id, COUNT(*) as count')
            ->groupBy('monitor_id')
            ->get()
            ->pluck('count', 'monitor_id')
            ->toArray();

        $monitors = Monitor::all();
        $labels = [];
        $datasets = [];
        $colors = [];

        foreach ($monitors as $monitor) {
            $count = $anomalies[$monitor->id] ?? 0;
            if ($count > 0) {
                $labels[] = $monitor->name;
                $datasets[] = $count;
                $colors[] = ResponseTime::generatePastelColorBasedOnMonitorId($monitor->id);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Incidents',
                    'data' => $datasets,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                ],
            ],
        ];
    }

    protected function getJsonChartOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'height' => 300,
        ];
    }

    public function getDescription(): ?string
    {
        return 'Incidents per monitor in the last 30 days';
    }



    protected function getType(): string
    {
        return 'bar';
    }
}
