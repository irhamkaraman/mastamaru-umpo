<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Filament Shield permissions, reset cache, and assign them.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Memulai sinkronisasi level DEWA untuk production...');

        /*
         * 1. Clear Laravel cache
         */
        $this->info('1. Membersihkan SEMUA cache bawaan Laravel...');

        Artisan::call('optimize:clear');
        $this->line(Artisan::output());

        /*
         * 2. Clear Spatie Permission cache
         */
        $this->info('2. Membersihkan cache Spatie Permission...');

        app(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        Artisan::call('permission:cache-reset');
        $this->line(Artisan::output());

        /*
         * 3. Generate Filament Shield permissions
         *
         * Shield dijalankan tanpa prompt interaktif.
         */
        $this->info('3. Men-generate ulang Filament Shield permissions...');

        $exitCode = Artisan::call('shield:generate', [
            '--all' => true,
            '--no-interaction' => true,
        ]);

        $this->line(Artisan::output());

        if ($exitCode !== 0) {
            $this->error(
                'Gagal menjalankan shield:generate. Exit code: '.$exitCode
            );

            return self::FAILURE;
        }

        /*
         * 4. Sync super_admin permissions
         */
        $this->info('4. Menyinkronkan semua permission ke role super_admin...');

        try {
            $superAdminRoleName = config(
                'filament-shield.super_admin.name',
                'super_admin'
            );

            $role = Role::firstOrCreate([
                'name' => $superAdminRoleName,
                'guard_name' => 'web',
            ]);

            $permissions = Permission::all();

            $role->syncPermissions($permissions);

            $this->info(
                '✅ Berhasil menyinkronkan '.
                $permissions->count().
                ' permission ke role '.
                $superAdminRoleName
            );
        } catch (Exception $e) {
            $this->error(
                'Gagal menyinkronkan role: '.$e->getMessage()
            );

            return self::FAILURE;
        }

        /*
         * 5. Refresh permission cache
         */
        $this->info('5. Refresh permission cache...');

        app(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        Artisan::call('permission:cache-reset');
        $this->line(Artisan::output());

        $this->info('🎉 Sinkronisasi permission selesai.');

        return self::SUCCESS;
    }
}
