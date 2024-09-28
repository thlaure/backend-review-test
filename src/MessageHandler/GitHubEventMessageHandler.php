<?php

namespace App\MessageHandler;

use App\Message\GitHubEventMessage;
use App\Service\GitHubEventProcessor;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GitHubEventMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private GitHubEventProcessor $eventProcessor
    ) {
    }

    public function __invoke(GitHubEventMessage $message): void
    {
        $this->eventProcessor->processEvent($message->getEventData());
    }
}
