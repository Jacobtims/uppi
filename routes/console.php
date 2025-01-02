<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('monitors:check')->everyMinute();
Schedule::command('cache:refresh-tasks')->everyMinute();
Schedule::command('checks:cleanup')->dailyAt('03:00');
