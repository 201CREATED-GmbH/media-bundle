<?php

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Model\Configuration\ObjectFieldConfiguration;
use C201\MediaBundle\Model\MediaLibrary;
use C201\MediaBundle\Model\Object\ObjectField;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\RouterInterface;

class StorageBackendMediaLibrary implements StorageBackendInterface
{
    /**
     * @var MediaLibrary
     */
    private $mediaLibrary;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param MediaLibrary    $mediaLibrary
     * @param RouterInterface $router
     */
    public function __construct(MediaLibrary $mediaLibrary, RouterInterface $router)
    {
        $this->mediaLibrary = $mediaLibrary;
        $this->router = $router;
    }

    /**
     * @param ObjectFieldConfiguration $config
     * @param ObjectField              $objectField
     * @param File                     $file
     *
     * @return bool|void
     */
    public function store(ObjectFieldConfiguration $config, ObjectField $objectField, File $file)
    {
        throw new \RuntimeException('Storing to storage backend is not implemented yet.');
    }

    /**
     * @param ObjectFieldConfiguration $config
     * @param ObjectField              $objectField
     * @param bool                     $preview
     *
     * @return string
     */
    public function getRelativePath(ObjectFieldConfiguration $config, ObjectField $objectField, $preview = false)
    {
        // handle media library items with a quick hack
        if (!preg_match('@^library:([^:]+):([^:]+)$@', $objectField->getValue(), $matches)) {
            return '';
        }

        list (, $id, $version) = $matches;
        if ($config->getStorageOption('controller', false)) {
            return $this->router->generate('c201_media_download', ['mediaId' => $id, 'version' => $version]);
        } else {
            $filename = $this->mediaLibrary->getMediaFilePath($id, $version);
            if ($preview) {
                if (preg_match('@\.(pdf)$@i', $filename)) {
                    $filename .= '.png';
                } elseif (preg_match('@\.(jpe?g|png)$@i', $filename)) {
                    // do nothing, not previewable
                } else {
                    return '';
                }
            }

            return $filename;
        }
    }
}
