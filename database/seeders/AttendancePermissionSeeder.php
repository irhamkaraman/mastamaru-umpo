<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AttendancePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat permission untuk export dan import attendance
        $permissions = [
            'export_attendance',
            'import_attendance',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Berikan permission ke role super_admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }

        // Pastikan role tamu TIDAK memiliki permission ini
        $tamuRole = Role::where('name', 'tamu')->first();
        if ($tamuRole) {
            // Hapus permission jika ada
            $tamuRole->revokePermissionTo($permissions);
        }

        $this->command->info('Permission export_attendance dan import_attendance berhasil dibuat!');
        $this->command->info('Role super_admin mendapat permission export dan import.');
        $this->command->info('Role tamu TIDAK memiliki permission export dan import.');
    }
}