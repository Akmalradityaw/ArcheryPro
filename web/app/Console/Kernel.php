<?php

namespace App\Console;

use App\Models\PengaturanSistem;
use App\Services\BackupService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ponytail: tanpa command/artisan baru — 1 closure baca pengaturan backup_otomatis.
        // WAJIB cron: * * * * * php artisan schedule:run di server agar jadwal hidup.
        $mode = PengaturanSistem::where('key_setting', 'backup_otomatis')->value('value_setting');
        if ($mode === 'off') {
            return;
        }
        $job = $schedule->call(fn () => app(BackupService::class)->run('json'))->name('backup-otomatis');
        match ($mode) {
            'mingguan' => $job->weekly(),
            'bulanan' => $job->monthly(),
            default => $job->daily(),
        };
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
