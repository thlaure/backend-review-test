<?php

namespace App\Service;

use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Repo;
use DateTimeImmutable;

class GitHubEventMapper
{
    public function map(array $data): Event
    {
        if (!isset($data['actor']) || !isset($data['actor']['id']) || !isset($data['actor']['login']) || !isset($data['actor']['url']) || !isset($data['actor']['avatar_url'])) {
            throw new \InvalidArgumentException('Invalid actor data');
        }

        $actor = Actor::fromArray($data['actor']);

        if (!isset($data['repo']) || !isset($data['repo']['id']) || !isset($data['repo']['name']) || !isset($data['repo']['url'])) {
            throw new \InvalidArgumentException('Invalid repo data');
        }

        $repo = Repo::fromArray($data['repo']);

        if (!isset($data['type']) || !array_key_exists($data['type'], EventType::EVENT_MAPPING)) {
            throw new \InvalidArgumentException('Invalid event type');
        }

        return new Event(
            $data['id'],
            EventType::EVENT_MAPPING[$data['type']],
            $actor,
            $repo,
            $data['payload'] ?? [],
            DateTimeImmutable::createFromFormat(DATE_W3C, $data['created_at']),
            $data['comment'] ?? null
        );
    }
}
