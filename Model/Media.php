<?php

namespace C201\MediaBundle\Model;

use C201\MediaBundle\Model\Configuration\MediaConfiguration;
use C201\MediaBundle\Model\Object\ObjectField;
use C201\MediaBundle\Model\Storage\StorageBackendPool;
use Symfony\Component\HttpFoundation\File\File;

class Media
{
    /**
     * @var MediaConfiguration
     */
    protected $configuration;

    /**
     * @var StorageBackendPool
     */
    protected $storageBackendPool;

    /**
     * @param MediaConfiguration $configuration
     * @param StorageBackendPool  $storageBackendPool
     */
    public function __construct(MediaConfiguration $configuration, StorageBackendPool $storageBackendPool)
    {
        $this->configuration = $configuration;
        $this->storageBackendPool = $storageBackendPool;
    }

    /**
     * Stores a file
     *
     * @param ObjectField  $objectField
     * @param File $file
     *
     * @return bool
     */
    public function store(ObjectField $objectField, File $file)
    {
        $config = $this->configuration->getObjectFieldConfiguration($objectField);

        return $this
            ->storageBackendPool
            ->getStorageBackend($config->getStorage())
                ->store($config, $objectField, $file);
    }

    /**
     * @param ObjectField $objectField
     * @param bool        $preview
     *
     * @return string
     */
    public function getPath(ObjectField $objectField, $preview = false)
    {
        $config = $this->configuration->getObjectFieldConfiguration($objectField);

        return $this
            ->storageBackendPool
            ->getStorageBackend($config->getStorage())
                ->getRelativePath($config, $objectField, $preview);
    }
}
