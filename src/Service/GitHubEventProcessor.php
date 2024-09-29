<?php

namespace App\Service;

use App\Entity\EventType;
use App\Message\DataEventsMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class GitHubEventProcessor
{
    public function __construct(
        private FileHandler $fileHandler,
        private MessageBusInterface $messageBus,
        private int $batchSize
    ) {
    }

    public function processEvents(string $filename): void
    {
        $dataEvents = new DataEventsMessage();
        $index = 0;

        foreach ($this->fileHandler->read($filename) as $line) {
            $eventData = json_decode($line, true);
            if (!is_array($eventData) || !isset(EventType::EVENT_MAPPING[$eventData['type']])) {
                continue;
            }

            $dataEvents->addEventData($eventData);

            if ($index++ % $this->batchSize === 0) {
                $this->messageBus->dispatch($dataEvents);
                $dataEvents = new DataEventsMessage();
            }
        }

        if (!empty($dataEvents->getEventsData())) {
            $this->messageBus->dispatch($dataEvents);
        }
    }
}
