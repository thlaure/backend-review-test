<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubDataFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function fetchEvents(string $url): iterable
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_GET, $url);
            foreach ($this->httpClient->stream($response) as $chunk) {
                yield $chunk->getContent();
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
