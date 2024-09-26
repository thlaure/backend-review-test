<?php

namespace App\Tests\Service;

use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Repo;
use App\Service\GitHubEventMapper;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class GitHubEventMapperTest extends TestCase
{
    private GitHubEventMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new GitHubEventMapper();
    }

    public function testMapSuccess(): void
    {
        $data = [
            'id' => '12345',
            'type' => 'PushEvent',
            'actor' => [
                'id' => '1234',
                'login' => 'actorLogin',
                'url' => 'http://actor.url',
                'avatar_url' => 'http://actor.avatar.url'
            ],
            'repo' => [
                'id' => '123',
                'name' => 'repoName',
                'url' => 'http://repo.url'
            ],
            'created_at' => '2024-09-26T12:34:56Z',
            'payload' => ['some' => 'data']
        ];

        $event = $this->mapper->map($data);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals(12345, $event->id());
        $this->assertEquals(EventType::EVENT_MAPPING['PushEvent'], $event->type());
        $this->assertInstanceOf(Actor::class, $event->actor());
        $this->assertEquals(1234, $event->actor()->id());
        $this->assertInstanceOf(Repo::class, $event->repo());
        $this->assertEquals(123, $event->repo()->id());
        $this->assertEquals(['some' => 'data'], $event->payload());
        $this->assertEquals(new DateTimeImmutable('2024-09-26T12:34:56Z'), $event->createAt());
    }

    public function testMapFailureInvalidActor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid actor data');

        $data = [
            'id' => '12345',
            'type' => 'PushEvent',
            // Missing 'actor' data
            'repo' => [
                'id' => '123',
                'name' => 'repoName',
                'url' => 'http://repo.url'
            ],
            'created_at' => '2024-09-26T12:34:56Z',
        ];

        $this->mapper->map($data);
    }

    public function testMapFailureInvalidRepo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid repo data');

        $data = [
            'id' => '12345',
            'type' => 'PushEvent',
            'actor' => [
                'id' => '123',
                'login' => 'actorLogin',
                'url' => 'http://actor.url',
                'avatar_url' => 'http://actor.avatar.url'
            ],
            // Missing 'repo' data
            'created_at' => '2024-09-26T12:34:56Z',
        ];

        $this->mapper->map($data);
    }

    public function testMapFailureInvalidEventType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid event type');

        $data = [
            'id' => '12345',
            'type' => 'InvalidEventType',
            'actor' => [
                'id' => '123',
                'login' => 'actorLogin',
                'url' => 'http://actor.url',
                'avatar_url' => 'http://actor.avatar.url'
            ],
            'repo' => [
                'id' => '123',
                'name' => 'repoName',
                'url' => 'http://repo.url'
            ],
            'created_at' => '2024-09-26T12:34:56Z',
        ];

        $this->mapper->map($data);
    }
}