<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Employee;
use App\Models\Customer;

class populatePermissionRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdminRole = Role::create(['name' => 'admin']);
        $roleManagerRole = Role::create(['name' => 'manager']);
        $roleEmployeeRole = Role::create(['name' => 'employee']);
        $roleCustomerRole = Role::create(['name' => 'customer']);
        
        Permission::create(['name' => 'create']);
        Permission::create(['name' => 'read']);
        Permission::create(['name' => 'update']);
        Permission::create(['name' => 'delete']);

        $roleAdmin = Role::findByName('admin');
        $roleAdmin->givePermissionTo('create');
        $roleAdmin->givePermissionTo('read');
        $roleAdmin->givePermissionTo('update');
        $roleAdmin->givePermissionTo('delete');

        $roleCustomer = Role::findByName('customer');
        $roleCustomer->givePermissionTo('read');

        $roleEmployee = Role::findByName('employee');
        $roleEmployee->givePermissionTo('create');
        $roleEmployee->givePermissionTo('read');
        $roleEmployee->givePermissionTo('update');
        $roleEmployee->givePermissionTo('delete');

        $roleManager = Role::findByName('manager');
        $roleManager->givePermissionTo('create');
        $roleManager->givePermissionTo('read');
        $roleManager->givePermissionTo('update');
        $roleManager->givePermissionTo('delete');

        $hashedPassword = hash('sha256', 'password123');

        $admin = Employee::create([
            'name' => 'admin',
            'employee_email' => 'testing@gmail.com',
            'employee_password' => $hashedPassword,
            'employee_number' => '1234567890',
            'role_id' => $roleAdminRole->id,
        ]);

        $employee = Employee::create([
            'name' => 'employee pat',
            'employee_email' => '123@gmail.com',
            'employee_password' => $hashedPassword,
            'employee_number' => '1234567890',
            'role_id' => $roleEmployeeRole->id,
        ]);

        $customer = Customer::create([
            'customer_name' => 'customer pat',
            'customer_email' => '789@gmail.com',
            'customer_password' => $hashedPassword,
            'customer_number' => '1234567890',
            'role_id' => $roleCustomerRole->id,
        ]);


    }
}
