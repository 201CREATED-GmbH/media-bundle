<?php

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Model\Configuration\ObjectFieldConfiguration;
use C201\MediaBundle\Model\Object\ObjectField;
use Symfony\Component\HttpFoundation\File\File;

interface StorageBackendInterface
{
    /**
     * @param ObjectFieldConfiguration $config
     * @param ObjectField              $objectField
     * @param File                     $file
     *
     * @return bool
     */
    public function store(ObjectFieldConfiguration $config, ObjectField $objectField, File $file);

    /**
     * @param ObjectFieldConfiguration $config
     * @param ObjectField              $objectField
     * @param bool                     $preview
     *
     * @return string
     */
    public function getRelativePath(ObjectFieldConfiguration $config, ObjectField $objectField, $preview = false);
}
