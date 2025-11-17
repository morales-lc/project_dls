<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class ExpireGuestAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guests:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired guest accounts as expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCount = User::where('role', 'guest')
            ->where('guest_account_status', 'active')
            ->where('guest_expires_at', '<', Carbon::now())
            ->update(['guest_account_status' => 'expired']);

        $this->info("Marked {$expiredCount} guest account(s) as expired.");
        
        return Command::SUCCESS;
    }
}
