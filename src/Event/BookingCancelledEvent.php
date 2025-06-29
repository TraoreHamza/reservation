<?php

namespace App\Event;

use App\Entity\Booking;
use Symfony\Contracts\EventDispatcher\Event;

class BookingCancelledEvent extends Event
{
    public const NAME = 'booking.cancelled';

    public function __construct(private Booking $booking) {}

    public function getBooking(): Booking
    {
        return $this->booking;
    }
}
