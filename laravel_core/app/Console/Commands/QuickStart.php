<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class QuickStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quickstart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One-command setup for the production portal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║          PRODUCTION PORTAL - QUICK START                       ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Step 1: Migrations
        $this->info('📦 Step 1: Running Migrations...');
        Artisan::call('migrate:fresh');
        $this->line('   ✓ Database tables created');
        $this->newLine();

        // Step 2: Seeding
        $this->info('👥 Step 2: Creating Test Users...');
        Artisan::call('db:seed', ['--class' => 'PortalTestSeeder']);
        $this->line('   ✓ 6 test users created');
        $this->newLine();

        // Step 3: Verification
        $this->info('🔍 Step 3: Verifying Setup...');
        Artisan::call('portal:verify');
        $this->newLine();

        // Summary
        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║          ✅ SETUP COMPLETE - YOU\'RE READY TO TEST!            ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->info('📋 Next: Start the server');
        $this->line('   <fg=cyan>php artisan serve</>');
        $this->newLine();

        $this->info('📖 Read the complete guide:');
        $this->line('   <fg=cyan>cat TESTING_GUIDE.md</>');
        $this->newLine();

        $this->info('🚀 Quick Test URLs:');
        $this->line('   <fg=cyan>http://localhost:8000/login</> (customer)');
        $this->line('   <fg=cyan>http://localhost:8000/admin/login</> (admin)');
        $this->newLine();

        return self::SUCCESS;
    }
}
