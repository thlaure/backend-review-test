<?php

namespace App\Entity;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EventType extends AbstractEnumType
{
    public const COMMIT = 'COM';
    public const COMMENT = 'MSG';
    public const PULL_REQUEST = 'PR';

    protected static array $choices = [
        self::COMMIT => 'Commit',
        self::COMMENT => 'Comment',
        self::PULL_REQUEST => 'Pull Request',
    ];

    public const PUSH_EVENT = 'PushEvent';
    public const COMMIT_COMMENT_EVENT = 'CommitCommentEvent';
    public const PR_EVENT = 'PullRequestEvent';
    public const PR_REVIEW_COMMENT_EVENT = 'PullRequestReviewCommentEvent';

    public const EVENT_MAPPING = [
        self::PUSH_EVENT => self::COMMIT,
        self::COMMIT_COMMENT_EVENT => self::COMMENT,
        self::PR_EVENT => self::PULL_REQUEST,
        self::PR_REVIEW_COMMENT_EVENT => self::COMMENT,
    ];
}
