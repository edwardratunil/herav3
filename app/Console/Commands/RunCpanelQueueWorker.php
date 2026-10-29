<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class RunCpanelQueueWorker extends Command
{
    protected $signature = 'cpanel:queue-run {--max-time=50} {--max-jobs=30}';
    protected $description = 'Safely runs a bounded database queue worker suitable for single cPanel cron without daemons';

    public function handle(): int
    {
        $maxTime = (int) $this->option('max-time');
        $maxJobs = (int) $this->option('max-jobs');

        // Atomic lock to guarantee no overlapping queue workers on shared cPanel hosting
        $lock = Cache::lock('cpanel_queue_worker_lock', $maxTime + 10);

        if (!$lock->get()) {
            $this->info('A queue worker is already running on this host. Exiting gracefully.');
            return Command::SUCCESS;
        }

        try {
            $this->info("Starting bounded queue worker (Max time: {$maxTime}s, Max jobs: {$maxJobs})...");
            
            Artisan::call('queue:work', [
                '--stop-when-empty' => true,
                '--max-time'        => $maxTime,
                '--max-jobs'        => $maxJobs,
                '--tries'           => 3,
                '--rest'            => 1,
            ]);

            $this->info('Queue worker finished execution.');
        } finally {
            $lock->release();
        }

        return Command::SUCCESS;
    }
}

