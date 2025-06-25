<?php

namespace App\Message;

class BookingReminderMessage
{
    public function __construct(
        private int $bookingId,
        private int $minutesBefore = 10
    ) {}

    public function getBookingId(): int
    {
        return $this->bookingId;
    }

    public function getMinutesBefore(): int
    {
        return $this->minutesBefore;
    }
}
