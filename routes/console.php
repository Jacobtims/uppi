<?php

Illuminate\Support\Facades\Schedule::command('monitors:check')->everyMinute();
Illuminate\Support\Facades\Schedule::command('cache:refresh-tasks')->everyMinute();
