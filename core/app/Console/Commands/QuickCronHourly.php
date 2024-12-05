<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\Upgrade;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QuickCronHourly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:quick-cron-hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run QuickCMS Cron Job hourly';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /* Delete old unpaid transactions */
        Transaction::where('created_at', '<=', Carbon::now()->subHours(3))
            ->where('status', null)
            ->delete();


        /* Reset user's plan on plan expiration */
        $upgrades = Upgrade::where('upgrade_expires', '<=', Carbon::now()->timestamp);

        foreach ($upgrades->get() as $upgrade){
            if($upgrade->user){
                /* reset user's plan */
                $upgrade->user->group_id = 'free';
                $upgrade->user->save();
            }
        }
        /* delete expired upgrades */
        $upgrades->delete();

        return 0;
    }
}
