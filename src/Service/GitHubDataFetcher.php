<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubDataFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function fetchEvents(string $url): ?string
    {
        $response = $this->httpClient->request(Request::METHOD_GET, $url);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception('Failed to fetch GitHub events');
        }

        return gzdecode($response->getContent());
    }
}
