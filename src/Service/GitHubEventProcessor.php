<?php

namespace App\Service;

use App\Entity\EventType;
use App\Repository\DbalWriteEventRepository;
use App\Service\GitHubEventMapper;

class GitHubEventProcessor
{
    public function __construct(
        private GitHubEventMapper $mapper,
        private DbalWriteEventRepository $eventRepository
    ) {
    }

    public function processEvent(array $eventData): void
    {
        if (!isset($eventData['type']) || !array_key_exists($eventData['type'], EventType::EVENT_MAPPING)) {
            return;
        }

        $event = $this->mapper->map($eventData);
        $this->eventRepository->insert($event);
    }
}
