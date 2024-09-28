<?php

namespace App\Message;

class GitHubEventMessage
{
    public function __construct(
        private array $eventData
    ) {
    }

    public function getEventData(): array
    {
        return $this->eventData;
    }
}
