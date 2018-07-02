<?php

namespace C201\MediaBundle\Model;

use C201\MediaBundle\Entity\File as FileDocument;
use C201\MediaBundle\Entity\Media as MediaDocument;
use C201\MediaBundle\Manager\MediaManager;
use C201\MediaBundle\Model\Configuration\MediaConfiguration;
use C201\MediaBundle\Model\Exception\FileAlreadyExistsException;
use C201\MediaBundle\Model\Storage\CarefulFileStorer;
use Symfony\Component\HttpFoundation\File\File;

class MediaLibrary
{
    /**
     * @var MediaConfiguration
     */
    private $configuration;

    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * @var CarefulFileStorer
     */
    private $storer;

    /**
     * @var FilesystemHelper
     */
    private $filesystemHelper;

    /**
     * @param MediaConfiguration        $configuration
     * @param MediaManager              $mediaManager
     * @param CarefulFileStorer $storer
     * @param FilesystemHelper          $filesystemHelper
     */
    public function __construct(
        MediaConfiguration $configuration,
        MediaManager $mediaManager,
        CarefulFileStorer $storer,
        FilesystemHelper $filesystemHelper
    ) {
        $this->configuration = $configuration;
        $this->mediaManager = $mediaManager;
        $this->storer = $storer;
        $this->filesystemHelper = $filesystemHelper;
    }

    /**
     * @param               $owner
     * @param File          $file
     * @param MediaDocument $media
     * @param string          $context
     *
     * @return MediaDocument
     *
     * @throws FileAlreadyExistsException
     */
    public function uploadFile($owner, File $file, MediaDocument $media = null, $context = null)
    {
        if ($mediaDuplicate = $this->mediaManager->findOneByOwnerAndFile($owner, $file)) {
            throw new FileAlreadyExistsException($mediaDuplicate);
        }

        if (null === $media) {
            $media = $this->mediaManager->createMedia($owner, $file, $context);
        }

        $fileD = $this->mediaManager->addFile($media, $file);

        $filename = $this->storer->store(
            $file,
            'media/'.$this->filesystemHelper->filesystemize($media->getId())
        );

        $fileD->setPath($filename);

        $this->mediaManager->save($media);

        return $media;
    }

    /**
     * @param MediaDocument $media
     * @param string        $context
     */
    public function changeContext(MediaDocument $media, $context)
    {
        if ($media->getDeletedAt()) {
            $media->undelete();
        }

        $media->setContext($context);

        $this->mediaManager->save($media);
    }

    /**
     * Retrieves the path relative to the upload directory
     *
     * @param string $media
     * @param string $version
     * @param bool   $absolute
     * @param bool   $preview
     *
     * @return string
     */
    public function getMediaFilePath($media, $version = FileDocument::VERSION_HEAD, $absolute = false, $preview = false)
    {
        if (!$media instanceof MediaDocument) {
            $media = $this->mediaManager->findOneById($media);
        }

        if (!$file = $media->getFileVersion($version)) {
            return '';
        }

        $filename = $file->getPath();
        if ($preview) {
            if (preg_match('@\.(pdf)$@i', $filename)) {
                $filename .= '.png';
            } elseif (preg_match('@\.(jpe?g|png)$@i', $filename)) {
                // do nothing, not previewable
            } else {
                return '';
            }
        }

        return '/'.ltrim(
            sprintf(
                '/%s/media/%s/%s',
                $absolute ? $this->configuration->getBaseUploadPath() : 'uploads',
                $this->filesystemHelper->filesystemize($media->getId()),
                $filename
            ),
            '/'
        );
    }
}
