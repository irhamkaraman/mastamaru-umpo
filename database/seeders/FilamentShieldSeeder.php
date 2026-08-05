<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use BezhanSalleh\FilamentShield\Support\Utils;

class FilamentShieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role super_admin jika belum ada
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        // Buat role tamu jika belum ada
        $tamuRole = Role::firstOrCreate([
            'name' => 'tamu',
            'guard_name' => 'web'
        ]);

        // Generate permissions untuk semua resources yang ada
        $resourcePermissions = [
            'attendance',
            'group', 
            'mentor',
            'user',
            'role',
            'permission'
        ];

        foreach ($resourcePermissions as $resource) {
            // Buat permissions untuk setiap resource
            $permissions = [
                "view_any_{$resource}",
                "view_{$resource}", 
                "create_{$resource}",
                "update_{$resource}",
                "delete_{$resource}",
                "delete_any_{$resource}",
                "force_delete_{$resource}",
                "force_delete_any_{$resource}",
                "restore_{$resource}",
                "restore_any_{$resource}",
                "replicate_{$resource}",
                "reorder_{$resource}",
                "export_{$resource}",
                "import_{$resource}"
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web'
                ]);
            }
        }

        // Berikan semua permissions ke super_admin
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        // Berikan permissions terbatas ke role tamu
        $tamuPermissions = [
            'view_any_attendance',
            'view_attendance',
            'view_any_group',
            'view_group',
            'view_any_mentor',
            'view_mentor'
        ];

        $tamuRole->syncPermissions($tamuPermissions);

        $this->command->info('Filament Shield roles dan permissions berhasil dibuat!');
        $this->command->info('Role super_admin: ' . $superAdminRole->permissions->count() . ' permissions');
        $this->command->info('Role tamu: ' . $tamuRole->permissions->count() . ' permissions');
    }
}