<?php

namespace App\Listeners;

use App\Models\LogAktivitas;
use Illuminate\Auth\Events\Login;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        LogAktivitas::catat('Login', "{$event->user->name} masuk ke dalam sistem.");
    }
}