<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
    public function handle()
    {
        $this->info('🚀 Memulai sinkronisasi level DEWA untuk production...');
        $this->info('1. Membersihkan SEMUA cache bawaan Laravel...');
        Artisan::call('optimize:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->line(Artisan::output());
        $this->info('2. Membersihkan cache Spatie Permission secara paksa...');
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('permission:cache-reset');
        $this->line(Artisan::output());
        $this->info('3. Men-generate ulang Filament Shield permissions...');
        Artisan::call('shield:generate', ['--all' => true]);
        $this->line(Artisan::output());
        $this->info('4. Memaksa role "super_admin" untuk mendapatkan semua permission...');
        $superAdminRoleName = config('filament-shield.super_admin.name', 'super_admin');
        try {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $superAdminRoleName, 'guard_name' => 'web']);
            $permissions = \Spatie\Permission\Models\Permission::all();
            $role->syncPermissions($permissions);
            $this->info('✅ Berhasil menyinkronkan '.$permissions->count().' permission ke role '.$superAdminRoleName);
        } catch (Exception $e) {
            $this->error('Gagal menyinkronkan role: '.$e->getMessage());
        }
        $this->info('🎉 Selesai! Silakan refresh halaman browser Anda.');
    }
}
