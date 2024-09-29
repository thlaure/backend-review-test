<?php

use App\Service\GitHubDataFetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GitHubDataFetcherTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private GitHubDataFetcher $dataFetcher;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->dataFetcher = new GitHubDataFetcher($this->httpClient);
    }

    public function testFetchEventsSuccess()
    {
        $url = 'https://data.gharchive.org/2015-01-01-01.json.gz';

        $this->httpClient->expects($this->any())
            ->method('request')
            ->with(Request::METHOD_GET, $url)
            ->willReturn($this->createMock(ResponseInterface::class));

        $events = $this->dataFetcher->fetchEvents($url);

        $this->assertIsIterable($events);
    }
}
