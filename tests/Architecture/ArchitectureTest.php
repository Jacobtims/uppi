<?php

arch()
    ->expect('App\Models')
    ->not->toUse([
        'App\Http\Controllers',
        'App\Console\Commands',
    ]);

arch()
    ->expect('App\Jobs')
    ->toImplement(\Illuminate\Contracts\Queue\ShouldQueue::class)
    ->ignoring(\App\Jobs\Checks\CheckJob::class);
arch()
    ->expect('App\Console\Commands')
    ->toBeClasses();

arch()
    ->expect('App\Models')
    ->toBeClasses();

arch()
    ->expect('App\Filament\Resources')
    ->toBeClasses();

arch()
    ->expect(['App'])
    ->not->toUse([
        'dd',
        'dump',
        'var_dump',
        'print_r'
    ]);

// Layer dependencies

arch()
    ->expect('App\CacheTasks')
    ->toBeClasses()
    ->ignoring(App\CacheTasks\RefreshStrategy::class)
    ->ignoring(App\CacheTasks\CacheTask::class);

