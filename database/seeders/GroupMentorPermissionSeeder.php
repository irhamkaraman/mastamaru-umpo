<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GroupMentorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat permissions untuk Group
        $groupPermissions = [
            'export_group',
            'import_group',
        ];

        // Buat permissions untuk Mentor
        $mentorPermissions = [
            'export_mentor',
            'import_mentor',
        ];

        // Gabungkan semua permissions
        $allPermissions = array_merge($groupPermissions, $mentorPermissions);

        // Buat permissions jika belum ada
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Berikan semua permissions kepada super_admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
            $this->command->info('Permissions berhasil diberikan kepada role super_admin');
        } else {
            $this->command->error('Role super_admin tidak ditemukan');
        }

        // Pastikan role tamu tidak memiliki permissions ini
        $tamuRole = Role::where('name', 'tamu')->first();
        if ($tamuRole) {
            $tamuRole->revokePermissionTo($allPermissions);
            $this->command->info('Permissions berhasil dihapus dari role tamu');
        }

        $this->command->info('Group dan Mentor permissions berhasil dibuat dan dikonfigurasi');
    }
}