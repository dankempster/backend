<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\Comment\Persistence\Dbal\Projector;

use Doctrine\DBAL\Connection;
use Ergonode\Core\Domain\Entity\AbstractId;
use Ergonode\EventSourcing\Infrastructure\DomainEventInterface;
use Ergonode\EventSourcing\Infrastructure\Exception\UnsupportedEventException;
use Ergonode\EventSourcing\Infrastructure\Projector\DomainEventProjectorInterface;
use Ergonode\Comment\Domain\Event\CommentCreatedEvent;

/**
 */
class CommentCreatedEventProjector implements DomainEventProjectorInterface
{
    private const TABLE =  'comment';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(DomainEventInterface $event): bool
    {
        return $event instanceof CommentCreatedEvent;
    }

    /**
     * @param AbstractId                               $aggregateId
     * @param DomainEventInterface|CommentCreatedEvent $event
     *
     * @throws UnsupportedEventException
     * @throws \Throwable
     */
    public function projection(AbstractId $aggregateId, DomainEventInterface $event): void
    {
        if (!$this->supports($event)) {
            throw new UnsupportedEventException($event, CommentCreatedEvent::class);
        }

        $this->connection->transactional(function () use ($aggregateId, $event) {
            $this->connection->insert(
                self::TABLE,
                [
                    'id' => $aggregateId->getValue(),
                    'author_id' => $event->getAuthorId()->getValue(),
                    'object_id' => $event->getObjectId()->toString(),
                    'created_at' => $event->getCreatedAt()->format('Y-m-d H:i:s'),
                    'content' => $event->getContent(),
                ]
            );
        });
    }
}