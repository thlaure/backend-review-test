<?php

namespace App\Service;

use App\Dto\ActorData;
use App\Dto\EventData;
use App\Dto\RepoData;

class GitHubEventMapper
{
    public function map(array $data): EventData
    {
        $actorData = new ActorData(
            $data['actor']['id'],
            $data['actor']['login'],
            $data['actor']['url'],
            $data['actor']['avatar_url']
        );

        $repoData = new RepoData(
            $data['repo']['id'],
            $data['repo']['name'],
            $data['repo']['url']
        );

        return new EventData(
            $data['id'],
            $data['type'],
            $data['public'],
            $data['payload']['action'] ?? '',
            $actorData,
            $repoData,
            new \DateTime($data['created_at'])
        );
    }
}
