<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Super administrador
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('super-admin');

        // Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Usuario editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name'     => 'Editor',
                'password' => Hash::make('password'),
            ]
        );
        $editor->assignRole('editor');

        // Usuarios de prueba adicionales (solo en local)
        if (app()->environment('local')) {
            User::factory(10)->create();
        }
    }
}
