<?php

namespace App\Livewire\Booking;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\View\View;
use Livewire\Component;

class ManageBooking extends Component
{
    public Booking $booking;

    public string $token = '';

    public ?string $message = null;

    public function mount(Booking $booking, string $token): void
    {
        abort_unless(hash_equals($booking->management_token, $token), 403);

        $this->booking = $booking;
        $this->token = $token;
    }

    public function requestRefund(BookingService $bookings): void
    {
        abort_unless(hash_equals($this->booking->management_token, $this->token), 403);

        if (! $this->booking->isRefundableByGuest()) {
            $this->message = __('Atmaksu vairs nevar veikt tiešsaistē. Lūdzu, sazinieties ar mums.');

            return;
        }

        $bookings->cancelAndRefund($this->booking, amount: null, reason: __('Viesa pieprasījums'));
        $this->booking->refresh();
        $this->message = __('Rezervācija atcelta un atmaksa veikta.');
    }

    public function render(): View
    {
        return view('livewire.booking.manage-booking');
    }
}
