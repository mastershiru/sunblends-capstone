<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeLogin extends Component
{
    public $email;
    public $password;
    public $remember = false;

    public function login()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('customer_email', $this->email)->first();
        $employee = Employee::where('employee_email', $this->email)->first();

        try {
            if (!$customer && !$employee) {
                $this->addError('email', 'Invalid Email or Password');
                return;
            }

            // Customer login logic (unchanged)
            if ($customer && hash('sha256', $this->password) === $customer->customer_password) {
                Auth::guard('customer')->login($customer);
                
                // Create Sanctum token
                $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;
                
                if ($this->remember) {
                    $rememberToken = Str::random(60);
                    $customer->update(['remember_token' => $rememberToken]);
                    Cookie::queue('remember_token', $rememberToken, 43200); // 30 days
                }

                Session::put('logged_in_customer', $customer);
                Session::put('token', $token);
                Session::put('guard', 'customer');
                
                $this->isOpen = false;
                $this->reset(['email', 'password']);
                
                return redirect('/dish')->with('success', 'Customer login successful!');
            }

            // Employee login logic (updated)
            if ($employee) {
                // Check for new password hashing format first
                if (Hash::check($this->password, $employee->employee_password)) {
                    // Modern password hash verified successfully
                    $this->processEmployeeLogin($employee);
                    return;
                }
                // If not found, try legacy hash
                else if (hash('sha256', $this->password) === $employee->employee_password) {
                    // Legacy password verified - update to new format
                    $employee->employee_password = Hash::make($this->password);
                    $employee->save();
                    
                    $this->processEmployeeLogin($employee);
                    return;
                }
            }

            // If no matching user was found or password didn't match
            $this->addError('email', 'Invalid Email or Password');
            
        } catch (\Exception $e) {
            $this->addError('email', 'An error occurred during login');
            \Log::error('Login error: ' . $e->getMessage());
        }
    }
    
    /**
     * Process employee login
     * Extracted to a separate method to avoid code duplication
     */
    private function processEmployeeLogin($employee)
    {
        // Clear any previous tokens
        $employee->tokens()->delete();
        
        Auth::guard('employee')->login($employee, $this->remember);
        
        // Get employee roles and permissions
        $roles = $employee->getRoleNames();
        $permissions = $employee->getAllPermissions()->pluck('name');
        
        // Create Sanctum token with employee abilities
        $token = $employee->createToken('employee-token', ['employee'])->plainTextToken;
        
        if ($this->remember) {
            $rememberToken = Str::random(60);
            $employee->update(['remember_token' => $rememberToken]);
            Cookie::queue('remember_token', $rememberToken, 43200); // 30 days
        }

        Session::put('logged_in_employee', $employee);
        Session::put('token', $token);
        Session::put('guard', 'employee');
        Session::put('roles', $roles);
        Session::put('permissions', $permissions);
        
        $this->isOpen = false;
        $this->reset(['email', 'password']);

        activity()
        ->causedBy($employee)
        ->withProperties(['ip_address' => request()->ip()])
        ->log('employee logged in');
        
        // Determine redirect based on role
        $redirectTo = '/dashboard';
        
        return redirect($redirectTo)->with([
            'success' => 'Employee login successful!',
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    public function render()
    {
        return view('livewire.employee-login');
    }
}