<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Model\Configuration\ObjectFieldConfiguration;
use C201\MediaBundle\Model\MediaLibrary;
use C201\MediaBundle\Model\Object\ObjectField;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\RouterInterface;

class StorageBackendMediaLibrary implements StorageBackendInterface
{
    public function __construct (
        private readonly MediaLibrary $mediaLibrary,
        private readonly RouterInterface $router,
    )
    {
    }


    public function store (ObjectFieldConfiguration $config, ObjectField $objectField, File $file) : bool
    {
        throw new \RuntimeException('Storing to storage backend is not implemented yet.');
    }


    public function getRelativePath (ObjectFieldConfiguration $config, ObjectField $objectField, bool $preview = false) : string
    {
        if (!\preg_match('@^library:([^:]+):([^:]+)$@', (string) $objectField->getValue(), $matches)) {
            return '';
        }

        [, $id, $version] = $matches;

        if ($config->getStorageOption('controller', false)) {
            return $this->router->generate('c201_media_download', ['mediaId' => $id, 'version' => $version]);
        }

        $filename = $this->mediaLibrary->getMediaFilePath($id, $version);

        if ($preview) {
            if (\preg_match('@\\.(pdf)$@i', $filename)) {
                $filename .= '.png';
            }
            elseif (\preg_match('@\\.(jpe?g|png)$@i', $filename)) {
                // not previewable
            }
            else {
                return '';
            }
        }

        return $filename;
    }
}
