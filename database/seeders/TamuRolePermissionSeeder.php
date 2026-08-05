<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TamuRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari role tamu
        $tamuRole = Role::where('name', 'tamu')->first();
        
        if (!$tamuRole) {
            $this->command->error('Role tamu tidak ditemukan!');
            return;
        }

        // Permission yang TIDAK boleh dimiliki role tamu
        $forbiddenPermissions = [
            'create_attendance',
            'update_attendance', 
            'delete_attendance',
            'delete_any_attendance',
            'export_attendance',
            'import_attendance',
            'restore_attendance',
            'restore_any_attendance',
            'replicate_attendance',
            'reorder_attendance',
            'force_delete_attendance',
            'force_delete_any_attendance',
        ];

        // Hapus semua permission yang tidak boleh dimiliki tamu
        foreach ($forbiddenPermissions as $permission) {
            if (Permission::where('name', $permission)->exists()) {
                $tamuRole->revokePermissionTo($permission);
            }
        }

        // Permission yang BOLEH dimiliki role tamu (hanya view)
        $allowedPermissions = [
            'view_attendance',
            'view_any_attendance',
        ];

        // Berikan hanya permission view kepada tamu
        foreach ($allowedPermissions as $permission) {
            if (Permission::where('name', $permission)->exists()) {
                $tamuRole->givePermissionTo($permission);
            }
        }

        $this->command->info('Permission role tamu berhasil diatur!');
        $this->command->info('Role tamu hanya memiliki permission: view_attendance, view_any_attendance');
        $this->command->info('Role tamu TIDAK memiliki permission: create, update, delete, export, import');
    }
}