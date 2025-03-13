<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthManager;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;


class CustomerNavbar extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $isDropdownLoginOpen = false;
    public $activeModal = null;
    public $cartItem;
    public $orders;

    public $email;
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $customer_number;
    public $customer_password;
    public $password_confirmation;
    public $password;
    public $new_profile_pic;
    public $new_password;
    public $new_password_confirmation;
    public $new_profile_picture;

    public $remember = false;

    protected $listeners = ['updateCount' => 'CartItemCount'];
    
    protected $rules = [
        'customer_name' => 'required|min:3',
        'customer_email' => 'required|email|unique:customer,customer_email',
        'customer_number' => 'required|numeric|digits_between:10,12',
        'customer_password' => 'required|min:6',
        'password_confirmation' => 'required|same:customer_password'
    ];

    public function ViewDetails($id)
    {
        $this->dispatch('viewDetail', $id);
    }
    
    public function cartOpen()
    {
        $this->dispatch('openCart');
    }

    public function toggleDropdownLogin()
    {
        $this->isDropdownLoginOpen = !$this->isDropdownLoginOpen;
    }

    public function setActiveModalLogin()
    {
        $this->activeModal = 'login';
        $this->isOpen = true;
    }
    

    public function setActiveModalRegister()
    {
        $this->activeModal = 'register';
        $this->isOpen = true;
    }

    public function setActiveModalOrder()
    {
        $this->getOrders();
        $this->activeModal = 'orders';
        $this->isOpen = true;

        
    }

    public function getOrders()
    {
        $this->orders = Order::where('customer_id', Auth::guard('customer')->user()->customer_id)->get();
    }

    public function setActiveModalAccount()
    {
        $this->activeModal = 'account';
        $this->isOpen = true;

        $customer = Customer::findOrFail(Auth::guard('customer')->user()->customer_id);
        $this->customer_name = $customer->customer_name;
        $this->customer_email = $customer->customer_email;
        $this->customer_number = $customer->customer_number;

    }

 

    public function updateAccount()
    {
        $rules = [
            'customer_name' => 'required|min:3',
            'customer_email' => 'required|email',
            'customer_number' => 'required|numeric|digits_between:10,12',
        ];

        // Only validate password if a new one is being set
        if ($this->new_password) {
            $rules['new_password'] = 'required|min:6';
            $rules['new_password_confirmation'] = 'required|same:new_password';
        }

        $this->validate($rules);

        $customer = Customer::findOrFail(Auth::guard('customer')->user()->customer_id);
        
        $updateData = [
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_number' => $this->customer_number,
        ];

        // Update password only if a new one is provided
        if ($this->new_password) {
            $updateData['customer_password'] = hash('sha256', $this->new_password);
        }

        // Handle profile picture upload if provided
        if ($this->new_profile_picture) {
            $path = $this->new_profile_picture->store('profile', 'public');
            $updateData['customer_picture'] ='storage/' . $path;
        }

        $customer->update($updateData);

        session()->flash('message', 'Account updated successfully!');
        $this->isOpen = false;
        $this->reset(['customer_name', 'customer_email', 'customer_number', 'new_password', 'new_password_confirmation', 'new_profile_picture']);
    }


    public function closeModal()
    {
        $this->resetInputFields();
        $this->isOpen = false;
        
    }

    public function resetInputFields()
    {
        $this->reset(['customer_name', 'customer_email', 'customer_number', 'customer_password', 'password_confirmation', 'email', 'password']);
    }

    public function mount()
    {
        $this->CartItemCount();
    }

    public function CartItemCount()
    {
        if(Auth::guard('customer')->check())
        {
            $this->cartItem = Cart::whereNotNull('customer_id')
            ->where('customer_id', Auth::guard('customer')->user()->customer_id)
            ->whereNull('deleted_at')
            ->count();
        }else{
            $this->cartItem = 0;
        }

    }

    public function store()
    {
        $validatedData = $this->validate([
            'customer_name' => 'required|min:3',
            'customer_email' => 'required|email|unique:customer,customer_email',
            'customer_number' => 'required|numeric',
            'customer_password' => 'required|min:6',
            'password_confirmation' => 'required|same:customer_password'
        ]);

        $customer = Customer::create([
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_number' => $this->customer_number,
            'customer_password' => hash('sha256', $this->customer_password),
            'role_id' => 4 
        ]);

        session()->flash('message', 'Registration successful!');
        
        $this->isOpen = false;
        $this->reset(['customer_name', 'customer_email', 'customer_number', 'customer_password', 'password_confirmation']);
    }

    public function GoToDashboard()
    {
        return redirect('/dashboard');
    }

    public function goToEmployeeLogin()
    {
        return redirect('/employee/login');
    }

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

    public function logout()
    {
        $user = Auth::guard('customer')->user();

        if ($user) {
            // Revoke all tokens for the user
            $user->tokens()->delete();
            
            
        }

        Auth::guard('customer')->logout();
        Auth::guard('employee')->logout();
        Session::forget('logged_in_customer');
        Session::forget('logged_in_employee');
        Session::forget('token');
        return redirect('/home');
    }

    public function render()
    {
        return view('livewire.customer-navbar', 
        [
            'cartItem' => $this->cartItem,
            'orders' => $this->orders
        ]);
    }
}


