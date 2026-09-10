<?php

namespace App\Listeners;

use App\Models\LogAktivitas;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        LogAktivitas::catat('Logout', "{$event->user->name} keluar dari sistem.");
    }
}