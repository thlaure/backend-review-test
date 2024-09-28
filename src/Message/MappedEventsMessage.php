<?php

namespace App\Message;

use App\Entity\Event;

class MappedEventsMessage
{
    public function __construct(
        private array $mappedEvents = [],
    ) {
    }

    public function getMappedEvents(): array
    {
        return $this->mappedEvents;
    }

    public function addMappedEvent(Event $mappedEvent): void
    {
        $this->mappedEvents[] = $mappedEvent;
    }
}
