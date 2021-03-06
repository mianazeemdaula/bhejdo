<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\EveryMinute::class,
        Commands\NewOpenOrder::class,
        Commands\ScheduleOrderMorning::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('every:mintue')->everyMinute();
        //$schedule->command('openorder:notifications')->cron("*/3 * * * *");
        // $schedule->command('openorder:notifications')->everyMinute();
        $schedule->command('subscription:orders')->cron("0 * * * *");
        //$schedule->command('openorder:notifications')->everyFiveMinutes();
        
        // Backup 
        $schedule->command('backup:clean')->dailyAt('23:00');
        $schedule->command('backup:run')->dailyAt('23:20');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
