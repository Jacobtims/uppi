<?php

namespace App\Filament\Widgets;

use App\Models\AlertTrigger;
use App\Models\Anomaly;
use App\Models\Monitor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatusWidget extends BaseWidget
{
    protected int|string|array $columnSpan = [
        'sm' => 12,
        'md' => 12,
        'lg' => 12,
    ];

    protected function getStats(): array
    {
        $anomalyGraph = Anomaly::where('ended_at', null)->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count')
            ->toArray();

        $monitorGraph = Monitor::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count')
            ->toArray();
        // only my own alert triggers
        $alertGraph = auth()->user()->alertTriggers()->selectRaw('DATE(alert_triggers.created_at) as date, COUNT(alert_triggers.id) as count')
            ->groupBy('date', 'user_id')
            ->orderBy('date')
            ->get()
            ->pluck('count')
            ->toArray();

        $anomalyCount = auth()->user()->anomalies()->where('ended_at', null)->count();

        return [
            Stat::make('Need attention', $anomalyCount)->chart($anomalyGraph)->color($anomalyCount > 0 ? 'primary-600' : 'success'),
            Stat::make('Total monitors', Monitor::count())->chart($monitorGraph),
            Stat::make('Incidents last 7 days', AlertTrigger::where('created_at', '>=', now()->subDays(7))->count())->chart($alertGraph),

        ];
    }
}
