<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('einvoice:auth')->everyMinute()->withoutOverlapping()->evenInMaintenanceMode();
Schedule::command('einvoice:check-status --limit=20')->everyMinute()->withoutOverlapping();
Schedule::command('einvoice:sync-cancel-doc')->everyMinute()->withoutOverlapping();
Schedule::command('einvoice:sync-rec-doc')->everyMinute()->withoutOverlapping();