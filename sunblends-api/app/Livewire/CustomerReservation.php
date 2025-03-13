<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use Livewire\Attributes\Validate;


class CustomerReservation extends Component
{
    public $isCustomerLogin = false;
    public $reservation;
    public $successModal = false;

    #[Validate('required|date')]
    public $reservation_date = '';

    #[Validate('required|date_format:H:i')]
    public $reservation_time = '';

    
    public $reservation_type = '';

    #[Validate('required|integer')]
    public $reservation_people;

    #[Validate('nullable|string')]
    public $reservation_status;

    
    public $order_id;


    public function checkCustomerLogin()
    {
        if(Auth::guard('customer')->check())
        {
            $this->isCustomerLogin = true;
        }
        else
        {
            $this->isCustomerLogin = false;
        }
    
    }

    public function closeModal()
    {
        $this->successModal = false;
    }

    public function store()
    {
        $this->validate();
        try {
           
            if(Auth::guard('customer')->check())
            {
                $customer = Auth::guard('customer')->user()->customer_id;
                
            }else{
                $customer = null;
            }
            
            Reservation::create([
                'reservation_date' => $this->reservation_date,
                'reservation_time' => $this->reservation_time,
                'reservation_type' => 'table',
                'reservation_people' => $this->reservation_people,
                'reservation_status' => 'pending', 
                'customer_id' => $customer 
            ]);

            session()->flash('message', 'Reservation created successfully.');
            $this->successModal = true;
            $this->reset();

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating reservation: ' . $e->getMessage());
            
        }
        
    }


    public function mount()
    {
        $this->checkCustomerLogin();
        
    }

    public function render()
    {
        
        return view('livewire.customer-reservation');
    }
}
