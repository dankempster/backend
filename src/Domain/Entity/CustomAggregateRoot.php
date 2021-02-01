<?php
namespace App\Domain\Entity;

use Ergonode\Attribute\Domain\ValueObject\AttributeCode;
use Ergonode\Value\Domain\Event\ValueAddedEvent;
use Ergonode\Value\Domain\Event\ValueChangedEvent;
use Ergonode\Value\Domain\Event\ValueRemovedEvent;
use Ergonode\Value\Domain\ValueObject\ValueInterface;
use App\Domain\Event\CustomAggregateRootCreatedEvent;
use Ergonode\Core\Domain\ValueObject\TranslatableString;
use Ergonode\EventSourcing\Domain\AbstractAggregateRoot;
use Ergonode\SharedKernel\Domain\AggregateId;
use JMS\Serializer\Annotation as JMS;
// use App\Domain\Event\BrandNameChangedEvent;
// use App\Domain\ValueObject\BrandCode;
use Webmozart\Assert\Assert;

class CustomAggregateRoot extends AbstractAggregateRoot
{
    private CustomAggregateRootId $id;

    /**
     * @var ValueInterface[]
     *
     * @JMS\Type("array<string, Ergonode\Value\Domain\ValueObject\ValueInterface>")
     */
    private array $attributes = array();

    /**
     * @param CustomAggregateRootId $id
     *
     * @throws \Exception
     */
    public function __construct(
        CustomAggregateRootId $id
    ) {
        $this->apply(new CustomAggregateRootCreatedEvent($id));
    }

    /**
     * @return AggregateId|CustomAggregateRootId
     */
    public function getId(): AggregateId
    {
        return $this->id;
    }

    public function hasAttribute(AttributeCode $attributeCode): bool
    {
        return isset($this->attributes[$attributeCode->getValue()]);
    }

    public function getAttribute(AttributeCode $attributeCode): ValueInterface
    {
        if ( ! $this->hasAttribute($attributeCode)) {
            throw new \RuntimeException(sprintf('Value for attribute %s not exists', $attributeCode->getValue()));
        }

        return clone $this->attributes[$attributeCode->getValue()];
    }

    /**
     * @throws \Exception
     */
    public function addAttribute(AttributeCode $attributeCode, ValueInterface $value): void
    {
        if ($this->hasAttribute($attributeCode)) {
            throw new \RuntimeException('Value already exists');
        }

        $this->apply(new ValueAddedEvent($this->id, $attributeCode, $value));
    }

    /**
     * @return ValueInterface[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @throws \Exception
     */
    public function changeAttribute(AttributeCode $attributeCode, ValueInterface $value): void
    {
        if ( ! $this->hasAttribute($attributeCode)) {
            throw new \RuntimeException('Value note exists');
        }

        if ((string) $this->attributes[$attributeCode->getValue()] !== (string) $value) {
            $this->apply(
                new ValueChangedEvent(
                    $this->id,
                    $attributeCode,
                    $value
                )
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function removeAttribute(AttributeCode $attributeCode): void
    {
        if ( ! $this->hasAttribute($attributeCode)) {
            throw new \RuntimeException('Value note exists');
        }

        $this->apply(new ValueRemovedEvent(
            $this->id,
            $attributeCode,
            $this->attributes[ $attributeCode->getValue() ]
        ));
    }

    protected function applyCustomAggregateRootCreatedEvent(CustomAggregateRootCreatedEvent $event): void
    {
        $this->id = $event->getAggregateId();
    }

    // protected function applyBrandNameChangedEvent(BrandNameChangedEvent $event): void
    // {
    //     $this->name = $event->getTo();
    // }

    protected function applyValueAddedEvent(ValueAddedEvent $event): void
    {
        $this->attributes[$event->getAttributeCode()->getValue()] = $event->getValue();
    }

    protected function applyValueChangedEvent(ValueChangedEvent $event): void
    {
        $this->attributes[$event->getAttributeCode()->getValue()] = $event->getTo();
    }

    protected function applyValueRemovedEvent(ValueRemovedEvent $event): void
    {
        unset($this->attributes[$event->getAttributeCode()->getValue()]);
    }
}
