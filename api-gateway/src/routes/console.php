<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:sync-users-cache')->everyFiveMinutes();

