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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*Daily*/
        /*quet store hang ngay xem co san pham moi khong*/
//        $schedule->call('App\Http\Controllers\WooController@scanStoreList')
//            ->dailyAt('00:30')->withoutOverlapping(10);
        /*Hourly*/
        /*luu san pham moi vao database*/
//        $schedule->call('App\Http\Controllers\WooController@scanProductNew')
//            ->everyFifteenMinutes()->between('00:10', '4:00')->withoutOverlapping(1);
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
