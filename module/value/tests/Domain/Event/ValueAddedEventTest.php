<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Value\Tests\Domain\Event;

use Ergonode\Attribute\Domain\ValueObject\AttributeCode;
use Ergonode\SharedKernel\Domain\Aggregate\CategoryId;
use Ergonode\Value\Domain\Event\ValueAddedEvent;
use Ergonode\Value\Domain\ValueObject\ValueInterface;
use PHPUnit\Framework\TestCase;

class ValueAddedEventTest extends TestCase
{
    public function testEventCreation(): void
    {
        /** @var CategoryId $id */
        $id = $this->createMock(CategoryId::class);
        /** @var AttributeCode $code */
        $code = $this->createMock(AttributeCode::class);
        /** @var ValueInterface $value */
        $value = $this->createMock(ValueInterface::class);

        $event = new ValueAddedEvent($id, $code, $value);
        $this->assertSame($id, $event->getAggregateId());
        $this->assertSame($code, $event->getAttributeCode());
        $this->assertSame($value, $event->getValue());
    }
}
