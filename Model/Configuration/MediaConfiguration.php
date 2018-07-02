<?php

namespace C201\MediaBundle\Model\Configuration;

use C201\MediaBundle\Model\Exception\NoObjectConfigurationAvailableException;
use C201\MediaBundle\Model\Exception\NoObjectFieldConfigurationAvailableException;
use C201\MediaBundle\Model\Object\ObjectField;

class MediaConfiguration
{
    /**
     * @var string
     */
    private $baseUploadPath;

    /**
     * @var array
     */
    private $objects;

    /**
     * @param string $baseUploadPath
     * @param array  $objects
     */
    public function __construct($baseUploadPath, array $objects = [])
    {
        $this->baseUploadPath = $baseUploadPath;
        $this->objects = $objects;
    }

    /**
     * @param $object
     *
     * @return ObjectConfiguration
     *
     * @throws NoObjectConfigurationAvailableException
     */
    public function getObjectConfiguration($object)
    {
        // loop all configurations and find the first matching one
        foreach ($this->objects as $class => $configuration) {
            if (!is_a($object, $class)) {
                continue;
            }
            return new ObjectConfiguration($configuration);
        }

        $message = sprintf('Object "%s" is not configured for MediaBundle', get_class($object));
        throw new NoObjectConfigurationAvailableException($message);
    }

    /**
     * @param ObjectField $objectField
     *
     * @return ObjectFieldConfiguration
     *
     * @throws NoObjectConfigurationAvailableException
     * @throws NoObjectFieldConfigurationAvailableException
     */
    public function getObjectFieldConfiguration(ObjectField $objectField)
    {
        return $this
            ->getObjectConfiguration($objectField->getObject())
            ->getFieldConfiguration($objectField->getField());
    }

    /**
     * @return array
     */
    public function getClassesUnderControl()
    {
        return array_keys($this->objects);
    }

    /**
     * @param $object
     *
     * @return array
     */
    public function getFieldsUnderControl($object)
    {
        try {
            return $this->getObjectConfiguration($object)->getFields();
        } catch (NoObjectConfigurationAvailableException $e) {
            return [];
        }
    }

    /**
     * @return string
     */
    public function getBaseUploadPath()
    {
        return $this->baseUploadPath;
    }
}
