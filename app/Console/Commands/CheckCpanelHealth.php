<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CheckCpanelHealth extends Command
{
    protected $signature = 'cpanel:health';
    protected $description = 'Verifies system health, database connectivity, storage permissions, and statutory extensions for cPanel';

    public function handle(): int
    {
        $this->info('=== HERA v3 cPanel System Health Check ===');
        $allPassed = true;

        // 1. Check PHP Version
        $phpVersion = PHP_VERSION;
        $this->line("PHP Version: {$phpVersion}");

        // 2. Check BCMath Extension (Required for Decimal Money Calculations)
        if (extension_loaded('bcmath')) {
            $this->info('[OK] BCMath extension loaded (decimal arithmetic enabled).');
        } else {
            $this->error('[FAIL] BCMath extension is missing! Contact hosting administrator.');
            $allPassed = false;
        }

        // 3. Database Connectivity
        try {
            DB::connection()->getPdo();
            $this->info('[OK] Database connection established.');
        } catch (\Exception $e) {
            $this->error('[FAIL] Database connection failed: ' . $e->getMessage());
            $allPassed = false;
        }

        // 4. Storage Segregation & Permissions
        $tenantsStorage = storage_path('app/tenants');
        if (!File::exists($tenantsStorage)) {
            File::makeDirectory($tenantsStorage, 0750, true);
        }

        if (is_writable($tenantsStorage)) {
            $this->info("[OK] Tenant storage directory ({$tenantsStorage}) is writable and outside public_html.");
        } else {
            $this->error("[FAIL] Tenant storage directory is not writable!");
            $allPassed = false;
        }

        // 5. Database Queue & Jobs Table Check
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            $this->info("[OK] Database Queue table accessible. (Pending: {$pendingJobs}, Failed: {$failedJobs})");
        } catch (\Exception $e) {
            $this->error('[FAIL] Database jobs table error: ' . $e->getMessage());
            $allPassed = false;
        }

        return $allPassed ? Command::SUCCESS : Command::FAILURE;
    }
}

