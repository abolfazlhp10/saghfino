<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ChangeAdStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:change-ads-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change the status of ads to 1';

    public function handle()
    {
        $adsToUpdate = Ad::withoutGlobalScopes()
            ->where('status', 0)
            ->limit(50)
            ->pluck('id');


        if ($adsToUpdate->isEmpty()) {
            Log::info("there is no ad with status = 0 to update");
            return;
        }

        $updatedCount = Ad::withoutGlobalScopes()
            ->whereIn('id', $adsToUpdate)
            ->update(['status' => 1]);

        Log::info("ads updated , total : " . $updatedCount);
    }
}
