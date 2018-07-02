<?php

namespace C201\MediaBundle\Model\Storage;

class StorageBackendPool
{
    protected $storageBackends = [];

    /**
     * @param string                  $key
     * @param StorageBackendInterface $storageBackend
     */
    public function addStorageBackend($key, StorageBackendInterface $storageBackend)
    {
        $this->storageBackends[$key] = $storageBackend;
    }

    /**
     * @param $key
     *
     * @return StorageBackendInterface
     */
    public function getStorageBackend($key)
    {
        if (!isset($this->storageBackends[$key])) {
            $message = sprintf('Storage Backend for key "%s" not available.', $key);
            throw new \OutOfBoundsException($message);
        }

        return $this->storageBackends[$key];
    }
}
