<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Model\Configuration\ObjectFieldConfiguration;
use C201\MediaBundle\Model\Object\ObjectField;
use Symfony\Component\HttpFoundation\File\File;

interface StorageBackendInterface
{
    public function store (ObjectFieldConfiguration $config, ObjectField $objectField, File $file) : bool;


    public function getRelativePath (ObjectFieldConfiguration $config, ObjectField $objectField, bool $preview = false) : string;
}
