<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Call Filament Shield seeder first to create roles
        $this->call([
            FilamentShieldSeeder::class,
            AttendancePermissionSeeder::class,
            GroupMentorPermissionSeeder::class,
            TamuRolePermissionSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@mastaumpo.com',
            'password' => bcrypt('12345678'),
        ]);

        // Assign super_admin role to administrator
        $admin->assignRole('super_admin');
    }
}
