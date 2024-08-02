<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Manager;
use App\Models\Employee;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create employee']);
        Permission::create(['name' => 'delete employee']);
        Permission::create(['name' => 'read employee']);
        Permission::create(['name' => 'update employee']);

        $employee = Role::create(['name' => 'employee']);
        $employee->givePermissionTo('create employee');
        $employee->givePermissionTo('read employee');

        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo(Permission::all());
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->has(Manager::factory())->create([
            'name' => 'Example Manager',
            'email' => 'manager@example.com',
        ]);
        $user->assignRole($manager);

        $user = \App\Models\User::factory()->has(Employee::factory())->create([
            'name' => 'Example Employee',
            'email' => 'employee@example.com',
        ]);
        $user->assignRole($employee);
    }
}
