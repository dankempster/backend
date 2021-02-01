<?php


namespace App\Domain\Event;


use Ergonode\SharedKernel\Domain\AggregateEventInterface;
use App\Domain\Entity\CustomAggregateRootId;
use Ergonode\SharedKernel\Domain\AggregateId;

class CustomAggregateRootCreatedEvent implements
    AggregateEventInterface
{
    private CustomAggregateRootId $id;
    
    public function __construct(CustomAggregateRootId $id)
    {
        $this->id = $id;
    }

    /**
     * @return AggregateId|CustomAggregateRootId
     */
    public function getAggregateId(): AggregateId
    {
        return $this->id;
    }
}
