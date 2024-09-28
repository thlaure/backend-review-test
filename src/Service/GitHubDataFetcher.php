<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubDataFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Filesystem $filesystem
    ) {
    }

    public function fetchEvents(string $url): \Generator
    {
        $response = $this->httpClient->request(Request::METHOD_GET, $url);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception('Failed to fetch GitHub events');
        }

        $content = gzdecode($response->getContent());
        if (false === $content) {
            throw new \Exception('Failed to decode GitHub events');
        }

        $stream = fopen('php://temp', 'r+');

        fwrite($stream, $content);
        rewind($stream);

        while (!feof($stream)) {
            yield fgets($stream);
        }
        ftruncate($stream, 0);
        rewind($stream);

        fclose($stream);
    }
}
