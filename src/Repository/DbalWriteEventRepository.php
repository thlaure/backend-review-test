<?php

namespace App\Repository;

use App\Dto\EventInput;
use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\Repo;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class DbalWriteEventRepository implements WriteEventRepository
{
    private Connection $connection;

    public function __construct(Connection $connection, private EntityManagerInterface $entityManager)
    {
        $this->connection = $connection;
    }

    public function update(EventInput $authorInput, int $id): void
    {
        $sql = <<<SQL
        UPDATE event
        SET comment = :comment
        WHERE id = :id
SQL;

        $this->connection->executeQuery($sql, ['id' => $id, 'comment' => $authorInput->comment]);
    }

    public function insert(Event $event): void
    {
        $this->connection->beginTransaction();

        try {
            $this->insertActor($event->actor());
            $this->insertRepo($event->repo());
            $this->insertEvent($event);
        } catch (\Exception $e) {
            $this->connection->rollback();
            throw $e;
        }

        $this->connection->commit();
    }

    private function insertActor(Actor $actor): void
    {
        $query = <<<SQL
        INSERT INTO actor (id, login, url, avatar_url) VALUES (:id, :login, :url, :avatar_url)
        ON CONFLICT (id)
            DO UPDATE SET login = :login, url = :url, avatar_url = :avatar_url
        ;
SQL;

        $this->connection->executeStatement($query, [
            'id' => $actor->id(),
            'login' => $actor->login(),
            'url' => $actor->url(),
            'avatar_url' => $actor->avatarUrl(),
        ]);
    }

    private function insertRepo(Repo $repo): void
    {
        $query = <<<SQL
        INSERT INTO repo (id, name, url) VALUES (:id, :name, :url)
        ON CONFLICT (id)
            DO UPDATE SET name = :name, url = :url
        ;
SQL;

        $this->connection->executeStatement($query, [
            'id' => $repo->id(),
            'name' => $repo->name(),
            'url' => $repo->url(),
        ]);
    }

    private function insertEvent(Event $event): void
    {
        $query = <<<SQL
        INSERT INTO event (id, type, actor_id, repo_id, payload, create_at, comment, count) VALUES (:id, :type, :actor_id, :repo_id, :payload, :create_at, :comment, :count)
        ON CONFLICT (id)
            DO UPDATE SET type = :type, actor_id = :actor_id, repo_id = :repo_id, payload = :payload, create_at = :create_at, comment = :comment
        ;
SQL;

        $this->connection->executeStatement($query, [
            'id' => $event->id(),
            'type' => $event->type(),
            'actor_id' => $event->actor()->id(),
            'repo_id' => $event->repo()->id(),
            'payload' => json_encode($event->payload()),
            'create_at' => $event->createAt()->format(DATE_W3C),
            'comment' => $event->getComment(),
            'count' => 1,
        ]);
    }
}
