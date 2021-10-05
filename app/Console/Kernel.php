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
        Commands\FetchStoreRings::class,
        Commands\FetchStoreZohoLeads::class,
        Commands\FetchUpdateZohoLeads::class,
        Commands\FetchStoreZohoContacts::class,
        Commands\FetchUpdateZohoContacts::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('auto:fetch_and_store_rings')->hourly();
        $schedule->command('auto:fetch_and_store_zoho_leads')->hourlyAt(10);
        $schedule->command('auto:fetch_and_update_zoho_leads')->hourlyAt(20);
        $schedule->command('auto:fetch_and_store_zoho_contacts')->hourlyAt(30);
        $schedule->command('auto:fetch_and_update_zoho_contacts')->hourlyAt(40);

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
