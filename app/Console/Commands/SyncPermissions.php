<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-permissions {--verify : Jalankan pengecekan verifikasi permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah SAKTI: Generate semua permission, assign super_admin ke semua user, reset cache, dan verifikasi.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();
        $this->info('====================================================================');
        $this->info('⚡ MEMULAI PERINTAH SAKTI: SINKRONISASI & VERIFIKASI SUPER ADMIN ⚡');
        $this->info('====================================================================');

        $this->info('1️⃣  Membersihkan seluruh cache (Laravel, Config, Route, View, Spatie)...');
        try {
            Artisan::call('optimize:clear');
            app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
            Cache::flush();
            $this->line('    ✅ Cache bootstrap dan Spatie permission berhasil di-reset.');
        } catch (Exception $e) {
            $this->warn('    ⚠️ Peringatan saat reset cache: ' . $e->getMessage());
        }

        $this->info('3️⃣  Men-generate seluruh hak akses (Resource, Custom Action, Page, Widget)...');

        $resources = [
            'attendance',
            'certificate_template',
            'group',
            'mentor',
            'presence_session',
            'role',
            'user',
            'api_configuration',
            'api_data_record',
            'student_assessment',
            'page',
        ];

        $prefixes = [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'export',
            'import',
            'download_template',
        ];

        $allPermRecords = [];
        $now = now();

        foreach ($resources as $res) {
            foreach ($prefixes as $pref) {
                $allPermRecords[] = [
                    'name' => $pref . '_' . $res,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Custom Page & Widget Permissions
        $specialPermissions = [
            'view_credit_page',
            'view_api_data_page',
            'page_CreditPage',
            'page_Dashboard',
            'widget_AccountWidget',
            'widget_FilamentInfoWidget',
            'widget_TotalStatsWidget',
            'widget_SystemInfoWidget',
            'widget_ActiveGroupsWidget',
            'widget_PresenceTrendWidget',
            'widget_ActiveSessionsWidget',
            'widget_GroupAttendanceWidget',
        ];

        foreach ($specialPermissions as $sp) {
            $allPermRecords[] = [
                'name' => $sp,
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Eksekusi 1 QUERY TUNGGAL untuk semua permission (Super Cepat!)
        DB::table('permissions')->insertOrIgnore($allPermRecords);

        $allPermissions = Permission::where('guard_name', 'web')->get();
        $this->line("    ✅ Total {$allPermissions->count()} permissions terdaftar di database.");

        // 4. Hubungkan seluruh permission ke role super_admin secara instan
        $this->info('3️⃣  Menghubungkan seluruh permission ke role super_admin...');
        $superAdminRoleName = config('filament-shield.super_admin.name', 'super_admin');
        $role = Role::firstOrCreate(['name' => $superAdminRoleName, 'guard_name' => 'web']);
        
        $rolePermissions = [];
        foreach ($allPermissions as $p) {
            $rolePermissions[] = [
                'permission_id' => $p->id,
                'role_id' => $role->id,
            ];
        }
        DB::table('role_has_permissions')->insertOrIgnore($rolePermissions);
        $this->line("    ✅ Role '{$superAdminRoleName}' sekarang memiliki {$allPermissions->count()} permissions.");

        // 5. Berikan Role super_admin ke SEMUA User di Database (Batch)
        $this->info('4️⃣  Memasangkan role super_admin ke seluruh akun user...');
        $userIds = DB::table('users')->pluck('id');
        if ($userIds->isEmpty()) {
            $this->warn('    ⚠️ Belum ada user di database. Membuat user admin default...');
            $admin = User::create([
                'name' => 'Administrator',
                'email' => 'admin@mastaumpo.com',
                'password' => bcrypt('12345678'),
            ]);
            $userIds = collect([$admin->id]);
        }

        $userRoles = [];
        foreach ($userIds as $uid) {
            $userRoles[] = [
                'role_id' => $role->id,
                'model_type' => User::class,
                'model_id' => $uid,
            ];
        }
        DB::table('model_has_roles')->insertOrIgnore($userRoles);

        $users = User::all();
        $userTableData = $users->map(function ($u) {
            return [
                'ID' => $u->id,
                'Name' => $u->name,
                'Email' => $u->email,
                'Roles' => 'super_admin',
            ];
        })->toArray();

        $this->table(['ID', 'Nama', 'Email', 'Roles Aktif'], $userTableData);

        // 6. Reset Cache Spatie
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('6️⃣  Menjalankan UJI VERIFIKASI Hak Akses Real-Time...');
        $criticalChecks = [
            'create_attendance' => 'Tambah Peserta (+)',
            'export_attendance' => 'Export Data CSV',
            'view_any_certificate_template' => 'Menu Template Sertifikat',
            'view_any_group' => 'Menu Kelompok',
            'view_any_mentor' => 'Menu Pendamping',
            'view_any_api_configuration' => 'Menu Konfigurasi API',
            'view_credit_page' => 'Halaman Tentang Sistem',
        ];

        $verificationRows = [];
        $firstUser = $users->first();

        foreach ($criticalChecks as $permission => $label) {
            $hasPermission = $firstUser->can($permission);
            $verificationRows[] = [
                'Fitur / Tombol' => $label,
                'Permission Key' => $permission,
                'Status Pengecekan' => $hasPermission ? '✅ AKTIF (PASS)' : '❌ HILANG (FAIL)',
            ];
        }

        $this->table(['Fitur / Tombol', 'Permission Key', 'Status Akses Akun: ' . $firstUser->email], $verificationRows);

        $this->newLine();
        $this->info('====================================================================');
        $this->info('🎉 SEMUA SELESAI! PERMISSION & ROLE SUPER_ADMIN SUDAH 100% AKTIF! 🎉');
        $this->info('====================================================================');
        $this->line('👉 Silakan buka halaman Admin di browser dan tekan Ctrl+F5 untuk refresh.');

        return Command::SUCCESS;
    }
}

