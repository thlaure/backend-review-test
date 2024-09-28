<?php

namespace App\MessageHandler;

use App\Message\MappedEventsMessage;
use App\Repository\DbalWriteEventRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MappedEventsMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private DbalWriteEventRepository $writeEventRepository
    ) {
    }

    public function __invoke(MappedEventsMessage $message): void
    {
        foreach ($message->getMappedEvents() as $mappedEvent) {
            $this->writeEventRepository->insert($mappedEvent);
        }
    }
}
