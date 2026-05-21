<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Object;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ObjectField
{
    private readonly PropertyAccessorInterface $accessor;


    public function __construct (
        private readonly object $object,
        private readonly string $field,
    )
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }


    public function getObject () : object
    {
        return $this->object;
    }


    public function getField () : string
    {
        return $this->field;
    }


    public function getValue () : mixed
    {
        return $this->accessor->getValue($this->object, $this->field);
    }


    public function setValue (mixed $value) : void
    {
        $this->accessor->setValue($this->object, $this->field, $value);
    }
}
