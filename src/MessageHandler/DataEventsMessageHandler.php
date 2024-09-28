<?php

namespace App\MessageHandler;

use App\Entity\EventType;
use App\Message\DataEventsMessage;
use App\Message\MappedEventsMessage;
use App\Service\GitHubEventMapper;
use App\Service\GitHubEventProcessor;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DataEventsMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private GitHubEventProcessor $eventProcessor,
        private MessageBusInterface $messageBus,
        private GitHubEventMapper $mapper,
        private int $batchSize,
    ) {
    }

    public function __invoke(DataEventsMessage $message): void
    {
        $dataEvents = $message->getEventsData();
        $mappedEvents = new MappedEventsMessage();
        foreach ($dataEvents as $index => $eventData) {
            if (!isset($eventData['type']) || !array_key_exists($eventData['type'], EventType::EVENT_MAPPING)) {
                continue;
            }

            $mappedEvents->addMappedEvent($this->mapper->map($eventData));

            if (($index + 1) % $this->batchSize === 0) {
                $this->messageBus->dispatch($mappedEvents);
            }
        }

        if (!empty($mappedEvents->getMappedEvents())) {
            $this->messageBus->dispatch($mappedEvents);
        }
    }
}
