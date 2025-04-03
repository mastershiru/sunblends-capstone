<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\Customer;

class populatePermissionRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing roles (created by RoleSeeder)
        $roleSuperAdmin = Role::where('name', 'Super Admin')->first();
        $roleManager = Role::where('name', 'Manager')->first();
        $roleEmployee = Role::where('name', 'Employee')->first();
        
        // Create customer role (not created by RoleSeeder)
        $roleCustomer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        
        // Create basic permissions
        Permission::firstOrCreate(['name' => 'create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'read', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'update', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete', 'guard_name' => 'web']);
        
        // Create additional permissions for specific areas
        Permission::firstOrCreate(['name' => 'manage employees', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view reports', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'export reports', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage menu', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage orders', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage reservations', 'guard_name' => 'web']);

        // Super Admin role - has all permissions
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // Manager role - has most permissions but not role management
        $roleManager->givePermissionTo(['create', 'read', 'update', 'delete', 'manage employees',
                                        'view reports', 'export reports', 'manage menu',
                                        'manage orders', 'manage reservations']);

        // Employee role - has basic operational permissions
        $roleEmployee->givePermissionTo(['create', 'read', 'update',
                                        'manage orders', 'manage reservations']);

        // Customer role - only has read permission
        $roleCustomer->givePermissionTo('read');

        // Use Laravel's Hash facade for secure password hashing
        $hashedPassword = Hash::make('password123');

        // Create Super Admin account
        $superAdmin = Employee::firstOrCreate(
            ['employee_email' => 'superadmin@sunblends.com'],
            [
                'name' => 'Super Admin',
                'employee_password' => $hashedPassword,
                'employee_number' => 'SA-2024-001',
                'role_id' => $roleSuperAdmin->id,
            ]
        );
        $superAdmin->assignRole($roleSuperAdmin);

        // Create Manager account
        $manager = Employee::firstOrCreate(
            ['employee_email' => 'manager@sunblends.com'],
            [
                'name' => 'Manager',
                'employee_password' => $hashedPassword,
                'employee_number' => 'MG-2024-001',
                'role_id' => $roleManager->id,
            ]
        );
        $manager->assignRole($roleManager);

        // Create Employee account
        $employee = Employee::firstOrCreate(
            ['employee_email' => 'employee@sunblends.com'],
            [
                'name' => 'Employee',
                'employee_password' => $hashedPassword,
                'employee_number' => 'EM-2024-001',
                'role_id' => $roleEmployee->id,
            ]
        );
        $employee->assignRole($roleEmployee);

        // For backwards compatibility, keep the original admin account
        $admin = Employee::firstOrCreate(
            ['employee_email' => 'testing@gmail.com'],
            [
                'name' => 'Admin',
                'employee_password' => $hashedPassword,
                'employee_number' => 'AD-2024-001',
                'role_id' => $roleSuperAdmin->id,
            ]
        );
        $admin->assignRole($roleSuperAdmin);

        // Customer account for completeness - use firstOrCreate to prevent duplicates
        $customer = Customer::firstOrCreate(
            ['customer_email' => '789@gmail.com'],
            [
                'customer_name' => 'Customer',
                'customer_password' => $hashedPassword,
                'customer_number' => '1234567890',
                'role_id' => $roleCustomer->id,
            ]
        );
        // If Customer model uses HasRoles trait:
        // $customer->assignRole($roleCustomer);
    }
}