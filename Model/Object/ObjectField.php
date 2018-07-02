<?php

namespace C201\MediaBundle\Model\Object;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectField
{
    private $object;
    private $field;

    private $accessor;

    /**
     * @param $object
     * @param $field
     */
    public function __construct($object, $field)
    {
        $this->object = $object;
        $this->field = $field;

        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Gets object
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Gets field
     *
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Sets value of one object field
     *
     * @return string
     */
    public function getValue()
    {
        return $this->accessor->getValue($this->object, $this->field);
    }

    /**
     * Sets value of one object field
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->accessor->setValue($this->object, $this->field, $value);
    }
}
