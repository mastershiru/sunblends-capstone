<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;

class ReservationDashboard extends Component
{
    #[Validate('required|string')]
    public $reservations;

    public $status;

    public $ReservationDetailModal = false;

    public $reservationDetail;

    public function mount()
    {
        $this->reservations = Reservation::all();
    }

    public function openDetailModal()
    {
        $this->ReservationDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->ReservationDetailModal = false;
    }

    public function ReservationViewDetails($id)
    {
        $this->openDetailModal();
        $this->reservationDetail = Reservation::find($id);
    }

    public function updateStatus($id, $value)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation) {
            $reservation->update([
                'reservation_status' => $value
            ]);

            $this->mount();
        }
    }

    public function render()
    {
        return view('livewire.reservation-dashboard', [
            'reservationDetail' => $this->reservationDetail 
        ]);
    }
}
