<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;


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
                
                $this->isOpen = false;
                $this->reset(['email', 'password']);
                
                return redirect('/dish')->with('success', 'Customer login successful!');
            }

            if ($employee && hash('sha256', $this->password) === $employee->employee_password) {
                Auth::guard('employee')->login($employee);
                
                // Create Sanctum token with employee abilities
                $token = $employee->createToken('employee-token', ['employee'])->plainTextToken;
                
                if ($this->remember) {
                    $rememberToken = Str::random(60);
                    $employee->update(['remember_token' => $rememberToken]);
                    Cookie::queue('remember_token', $rememberToken, 43200); // 30 days
                }

                Session::put('logged_in_employee', $employee);
                Session::put('token', $token);
                
                $this->isOpen = false;
                $this->reset(['email', 'password']);
                
                return redirect('/dashboard')->with('success', 'Employee login successful!');
            }

            $this->addError('email', 'Invalid Email or Password');
        } catch (\Exception $e) {
            $this->addError('email', 'An error occurred during login');
            \Log::error('Login error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employee-login');
    }
}
