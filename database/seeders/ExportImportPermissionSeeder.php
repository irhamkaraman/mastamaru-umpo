<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExportImportPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions untuk Attendance
        $attendancePermissions = [
            'export_attendance',
            'import_attendance',
            'download_template_attendance',
        ];

        // Permissions untuk Group
        $groupPermissions = [
            'export_group',
            'import_group',
            'download_template_group',
        ];

        // Permissions untuk Mentor
        $mentorPermissions = [
            'export_mentor',
            'import_mentor',
            'download_template_mentor',
        ];

        // Permissions untuk PresenceSession
        $presenceSessionPermissions = [
            'export_presence::session',
            'import_presence::session',
            'download_template_presence::session',
        ];

        // Gabungkan semua permissions
        $allPermissions = array_merge(
            $attendancePermissions,
            $groupPermissions,
            $mentorPermissions,
            $presenceSessionPermissions
        );

        // Buat permissions jika belum ada
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Berikan semua permissions ke role super_admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
        }

        $this->command->info('Export/Import permissions created and assigned to super_admin role.');
    }
}