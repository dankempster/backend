<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Product\Infrastructure\Persistence\Projector\Binding;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Ergonode\Product\Domain\Event\Bind\BindAddedToProductEvent;

class DbalBindAddedToProductEventProjector
{
    private const TABLE = 'product_binding';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws DBALException
     */
    public function __invoke(BindAddedToProductEvent $event): void
    {
        $this->connection->insert(
            self::TABLE,
            [
                'product_id' => $event->getAggregateId()->getValue(),
                'attribute_id' => $event->getAttributeId()->getValue(),
            ]
        );
    }
}
