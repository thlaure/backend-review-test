<?php

namespace App\Dto;

class EventData
{
    public function __construct(
        public string $id,
        public string $type,
        public string $public,
        public string $payloadAction,
        public ActorData $actor,
        public RepoData $repo,
        public \DateTime $createdAt
    ) {
    }
}
