<?php

namespace App\Filament\Widgets;

use App\Models\AlertTrigger;
use App\Models\Anomaly;
use App\Models\Monitor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatusWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 11;

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

        $alertGraph = AlertTrigger::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->where('created_at', '>=', now()->subDays(7))
            ->get()
            ->pluck('count')
            ->toArray();

            $anomalyCount = Anomaly::where('ended_at', null)->count();

        return [
            Stat::make('Need attention', $anomalyCount)->chart($anomalyGraph)->color($anomalyCount > 0 ? 'primary-600' : 'success'),
            Stat::make('Total monitors', Monitor::count())->chart($monitorGraph),
            Stat::make('Alerts last 7 days', AlertTrigger::where('created_at', '>=', now()->subDays(7))->count())->chart($alertGraph),


        ];
    }
}
