<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class VerifyPortalSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify the production portal split setup and run comprehensive checks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Verifying Production Portal Setup...');
        $this->newLine();

        // 1. Check routes
        $this->checkRoutes();

        // 2. Check migrations
        $this->checkMigrations();

        // 3. Check models
        $this->checkModels();

        // 4. Check middleware
        $this->checkMiddleware();

        // 5. Check views
        $this->checkViews();

        $this->newLine();
        $this->info('✅ Portal setup verification complete!');
        $this->newLine();

        $this->info('📋 Next Steps:');
        $this->line('1. Run migrations: <fg=cyan>php artisan migrate</>');
        $this->line('2. Seed test users: <fg=cyan>php artisan db:seed --class=PortalTestSeeder</>');
        $this->line('3. Run tests: <fg=cyan>php artisan test</>');
        $this->newLine();

        $this->info('🌐 Portal URLs:');
        $this->line('   Customer Login: <fg=cyan>http://localhost/login</>');
        $this->line('   Admin Login: <fg=cyan>http://localhost/admin/login</>');
        $this->line('   Customer Dashboard: <fg=cyan>http://localhost/dashboard</>');
        $this->line('   Admin Dashboard: <fg=cyan>http://localhost/admin</>');
        $this->newLine();

        $this->info('👥 Test Credentials:');
        $this->table(
            ['User Type', 'Email', 'Password', 'Portal'],
            [
                ['Customer', 'customer@example.com', 'password', '/login'],
                ['Admin', 'admin@example.com', 'password', '/admin/login'],
                ['Support Agent', 'support@example.com', 'password', '/admin/login'],
                ['Manager', 'manager@example.com', 'password', '/admin/login'],
                ['Ticketing Officer', 'ticketing@example.com', 'password', '/admin/login'],
                ['Accounts Officer', 'accounts@example.com', 'password', '/admin/login'],
            ]
        );

        return 0;
    }

    private function checkRoutes(): void
    {
        $this->line('<fg=yellow>Checking Routes...</>');

        $routes = [
            'login' => 'Customer Login',
            'register' => 'Customer Register',
            'dashboard' => 'Customer Dashboard',
            'admin.login' => 'Admin Login',
            'admin.otp.form' => 'Admin OTP Form',
            'admin.dashboard' => 'Admin Dashboard',
        ];

        foreach ($routes as $name => $label) {
            try {
                route($name);
                $this->line("  ✓ {$label}");
            } catch (\Exception $e) {
                $this->error("  ✗ {$label} - NOT FOUND");
            }
        }
    }

    private function checkMigrations(): void
    {
        $this->line('<fg=yellow>Checking Database Tables...</>');

        $tables = [
            'users' => 'Users Table',
            'password_reset_tokens' => 'Password Reset Tokens',
            'sessions' => 'Sessions',
            'roles' => 'Roles',
            'permissions' => 'Permissions',
            'role_permissions' => 'Role Permissions',
            'user_roles' => 'User Roles',
            'user_sessions' => 'User Sessions',
            'login_history' => 'Login History',
            'audit_logs' => 'Audit Logs',
            'refunds' => 'Refunds',
            'support_tickets' => 'Support Tickets',
            'coupons' => 'Coupons',
            'markups' => 'Markups',
            'notifications' => 'Notifications',
        ];

        foreach ($tables as $table => $label) {
            if (Schema::hasTable($table)) {
                $this->line("  ✓ {$label}");
            } else {
                $this->line("  ○ {$label} (not migrated yet)");
            }
        }
    }

    private function checkModels(): void
    {
        $this->line('<fg=yellow>Checking Models...</>');

        $models = [
            'App\\Models\\User' => 'User Model',
            'App\\Models\\Booking' => 'Booking Model',
            'App\\Models\\Payment' => 'Payment Model',
        ];

        foreach ($models as $class => $label) {
            if (class_exists($class)) {
                $this->line("  ✓ {$label}");
            } else {
                $this->error("  ✗ {$label} - NOT FOUND");
            }
        }

        // Check User methods
        $this->line('<fg=yellow>  Checking User Model Methods...</>');
        if (method_exists('App\\Models\\User', 'isCustomer')) {
            $this->line('    ✓ isCustomer()');
        }
        if (method_exists('App\\Models\\User', 'isInternalUser')) {
            $this->line('    ✓ isInternalUser()');
        }
    }

    private function checkMiddleware(): void
    {
        $this->line('<fg=yellow>Checking Middleware...</>');

        $middlewares = [
            'App\\Http\\Middleware\\EnsureUserHasRole' => 'Role Middleware',
        ];

        foreach ($middlewares as $class => $label) {
            if (class_exists($class)) {
                $this->line("  ✓ {$label}");
            } else {
                $this->error("  ✗ {$label} - NOT FOUND");
            }
        }
    }

    private function checkViews(): void
    {
        $this->line('<fg=yellow>Checking Views...</>');

        $views = [
            'auth.login' => 'Customer Login View',
            'auth.register' => 'Customer Register View',
            'auth.admin-login' => 'Admin Login View',
            'auth.admin-otp' => 'Admin OTP View',
            'dashboard' => 'Customer Dashboard View',
            'admin.dashboard' => 'Admin Dashboard View',
        ];

        foreach ($views as $view => $label) {
            if (view()->exists($view)) {
                $this->line("  ✓ {$label}");
            } else {
                $this->error("  ✗ {$label} - NOT FOUND");
            }
        }
    }
}
