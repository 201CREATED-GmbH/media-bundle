<?php declare(strict_types=1);

namespace C201\MediaBundle\Model;

use C201\MediaBundle\Model\Configuration\MediaConfiguration;
use C201\MediaBundle\Model\Exception\NoObjectConfigurationAvailableException;
use C201\MediaBundle\Model\Exception\NoObjectFieldConfigurationAvailableException;
use C201\MediaBundle\Model\Object\ObjectField;
use C201\MediaBundle\Model\Storage\StorageBackendPool;
use Symfony\Component\HttpFoundation\File\File;

class Media
{
    public function __construct (
        protected readonly MediaConfiguration $configuration,
        protected readonly StorageBackendPool $storageBackendPool,
    )
    {
    }


    /**
     * @throws NoObjectConfigurationAvailableException
     * @throws NoObjectFieldConfigurationAvailableException
     */
    public function store (ObjectField $objectField, File $file) : bool
    {
        $config = $this->configuration->getObjectFieldConfiguration($objectField);

        return $this->storageBackendPool
            ->getStorageBackend($config->getStorage())
            ->store($config, $objectField, $file);
    }


    /**
     * @throws NoObjectConfigurationAvailableException
     * @throws NoObjectFieldConfigurationAvailableException
     */
    public function getPath (ObjectField $objectField, bool $preview = false) : string
    {
        $config = $this->configuration->getObjectFieldConfiguration($objectField);

        return $this->storageBackendPool
            ->getStorageBackend($config->getStorage())
            ->getRelativePath($config, $objectField, $preview);
    }
}
