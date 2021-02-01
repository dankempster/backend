<?php


namespace Test\Domain\Entity;


use App\Domain\Entity\CustomAggregateRootId;
use App\Domain\Entity\CustomAggregateRoot;
use Ergonode\Attribute\Domain\ValueObject\AttributeCode;
use Ergonode\Value\Domain\ValueObject\StringValue;
use PHPUnit\Framework\TestCase;

class CustomAggregateRootTest extends TestCase
{
    private CustomAggregateRoot $fixture;

    public function setUp() : void
    {
        $id = $this->createMock(CustomAggregateRootId::class);

        $this->fixture = new CustomAggregateRoot($id);
    }

    private function addAttribute() : AttributeCode
    {
        $attributeCode = new AttributeCode('attrib_code');
        $attributeValue = new StringValue('foo bar');

        $this->fixture->addAttribute($attributeCode, $attributeValue);
    }
    
    public function testAttribute() : void
    {
        $attributeCode = new AttributeCode('attrib_code');
        $attributeValue = new StringValue('foo');

        $this->fixture->addAttribute($attributeCode, $attributeValue);
        $this->assertTrue($this->fixture->hasAttribute($attributeCode));
        $this->assertEquals($attributeValue, $this->fixture->getAttribute($attributeCode));

        $attributeAltValue = new StringValue('bar');
        $this->fixture->changeAttribute($attributeCode, $attributeAltValue);
        $this->assertEquals($attributeAltValue, $this->fixture->getAttribute($attributeCode));

        $this->fixture->removeAttribute($attributeCode);
        $this->assertFalse($this->fixture->hasAttribute($attributeCode));
    }
}
