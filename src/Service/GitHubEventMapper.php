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
        $actor = new Actor(
            $data['actor']['id'],
            $data['actor']['login'],
            $data['actor']['url'],
            $data['actor']['avatar_url']
        );

        $repo = new Repo(
            $data['repo']['id'],
            $data['repo']['name'],
            $data['repo']['url']
        );

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
