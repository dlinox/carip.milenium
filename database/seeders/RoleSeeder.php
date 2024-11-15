<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role1      = Role::create(['name'  => 'SUPERADMIN']);
        $role2      = Role::create(['name'  => 'ADMIN']);
        $role3      = Role::create(['name'  => 'VENDEDOR']);

        Permission::create([
            'name'          => 'admin.business', 
            'descripcion'   => 'Ver información de empresa'
        ])->syncRoles([$role1]);

        Permission::create([
            'name'          => 'admin.buys', 
            'descripcion'   => 'Ver compras'
        ])->syncRoles([$role1, $role2]);

        Permission::create([
            'name'          => 'admin.create_buy', 
            'descripcion'   => 'Registrar nueva compra'
        ])->syncRoles([$role1, $role2]);

        Permission::create([
            'name'          => 'admin.bills', 
            'descripcion'   => 'Ver gastos'
        ])->syncRoles([$role1, $role2]);

        Permission::create([
            'name'          => 'admin.products', 
            'descripcion'   => 'Ver productos'
        ])->syncRoles([$role1, $role2]);

        Permission::create([
            'name'          => 'admin.users', 
            'descripcion'   => 'Ver usuarios'
        ])->syncRoles([$role1, $role2]);

        Permission::create([
            'name'          => 'admin.roles', 
            'descripcion'   => 'Ver roles'
        ])->syncRoles([$role1, $role2]);

        Permission::create([
            'name'          => 'admin.prices', 
            'descripcion'   => 'Ver precios'
        ])->syncRoles([$role1, $role2]);

        Permission::create([
            'name'          => 'admin.faq', 
            'descripcion'   => 'Ver preguntas frecuentes'
        ])->syncRoles([$role1, $role2]);
    }
}
