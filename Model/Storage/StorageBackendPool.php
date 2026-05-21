<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage;

class StorageBackendPool
{
    /** @var StorageBackendInterface[] */
    protected array $storageBackends = [];


    public function addStorageBackend (string $key, StorageBackendInterface $storageBackend) : void
    {
        $this->storageBackends[$key] = $storageBackend;
    }


    public function getStorageBackend (string $key) : StorageBackendInterface
    {
        if (!isset($this->storageBackends[$key])) {
            throw new \OutOfBoundsException(\sprintf('Storage Backend for key "%s" not available.', $key));
        }

        return $this->storageBackends[$key];
    }
}
