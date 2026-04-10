<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Usuarios
            'users.view',
            'users.edit',
            'users.delete',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Proveedores
            'proveedores.view',
            'proveedores.create',
            'proveedores.edit',
            'proveedores.delete',

            // Documentos
            'documentos.view',
            'documentos.create',
            'documentos.delete',
            'documentos.estado',

            // Items SAP (solo lectura, BD remota)
            'itemssap.view',

            // Series SAP (solo lectura, BD remota)
            'seriessap.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Rol editor: solo lectura general
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions([
            'users.view',
            'proveedores.view',
            'documentos.view',
            'itemssap.view',
            'seriessap.view',
        ]);

        // Rol admin: gestión completa excepto usuarios/roles avanzados
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'users.view',
            'users.edit',
            'users.delete',
            'roles.view',
            'proveedores.view',
            'proveedores.create',
            'proveedores.edit',
            'proveedores.delete',
            'documentos.view',
            'documentos.create',
            'documentos.delete',
            'documentos.estado',
            'itemssap.view',
            'seriessap.view',
        ]);

        // Rol super-admin: todos los permisos
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());
    }
}
