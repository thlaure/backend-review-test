<?php

namespace App\Message;

class DataEventsMessage
{
    public function __construct(
        private array $eventsData = [],
    ) {
    }

    public function getEventsData(): array
    {
        return $this->eventsData;
    }

    public function addEventData(array $eventData): void
    {
        $this->eventsData[] = $eventData;
    }
}
