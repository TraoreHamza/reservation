<?php

namespace App\Event;

use App\Entity\Booking;
use Symfony\Contracts\EventDispatcher\Event;

class BookingValidatedEvent extends Event
{
    public const NAME = 'booking.validated';

    public function __construct(private Booking $booking) {}

    public function getBooking(): Booking
    {
        return $this->booking;
    }
}
