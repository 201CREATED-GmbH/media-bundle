<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Configuration;

use C201\MediaBundle\Model\Exception\NoObjectConfigurationAvailableException;
use C201\MediaBundle\Model\Exception\NoObjectFieldConfigurationAvailableException;
use C201\MediaBundle\Model\Object\ObjectField;

class MediaConfiguration
{
    public function __construct (
        private readonly string $baseUploadPath,
        private readonly array $objects = [],
    )
    {
    }


    /**
     * @throws NoObjectConfigurationAvailableException
     */
    public function getObjectConfiguration (object $object) : ObjectConfiguration
    {
        foreach ($this->objects as $class => $configuration) {
            if (!\is_a($object, $class)) {
                continue;
            }
            return new ObjectConfiguration($configuration);
        }

        throw new NoObjectConfigurationAvailableException(
            \sprintf('Object "%s" is not configured for MediaBundle', $object::class)
        );
    }


    /**
     * @throws NoObjectConfigurationAvailableException
     * @throws NoObjectFieldConfigurationAvailableException
     */
    public function getObjectFieldConfiguration (ObjectField $objectField) : ObjectFieldConfiguration
    {
        return $this
            ->getObjectConfiguration($objectField->getObject())
            ->getFieldConfiguration($objectField->getField());
    }


    /**
     * @return string[]
     */
    public function getClassesUnderControl () : array
    {
        return \array_keys($this->objects);
    }


    /**
     * @return string[]
     */
    public function getFieldsUnderControl (object $object) : array
    {
        try {
            return $this->getObjectConfiguration($object)->getFields();
        } catch (NoObjectConfigurationAvailableException) {
            return [];
        }
    }


    public function getBaseUploadPath () : string
    {
        return $this->baseUploadPath;
    }
}
