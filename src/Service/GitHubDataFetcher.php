<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubDataFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Filesystem $filesystem
    ) {
    }

    public function fetchEvents(string $url): iterable
    {
        $response = $this->httpClient->request(Request::METHOD_GET, $url);
        foreach ($this->httpClient->stream($response) as $chunk) {
            yield $chunk->getContent();
        }
    }
}
