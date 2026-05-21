<?php declare(strict_types=1);

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
    public function __construct (
        private readonly MediaConfiguration $configuration,
        private readonly MediaManager $mediaManager,
        private readonly CarefulFileStorer $storer,
        private readonly FilesystemHelper $filesystemHelper,
    )
    {
    }


    /**
     * @throws FileAlreadyExistsException
     */
    public function uploadFile (
        mixed $owner,
        File $file,
        ?MediaDocument $media = null,
        ?string $context = null,
    ) : ?MediaDocument
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
            'media/' . $this->filesystemHelper->filesystemize((string) $media->getId()),
        );

        $fileD->setPath($filename);

        $this->mediaManager->save($media);

        return $media;
    }


    public function changeContext (MediaDocument $media, string $context) : void
    {
        if ($media->getDeletedAt()) {
            $media->undelete();
        }

        $media->setContext($context);
        $this->mediaManager->save($media);
    }


    public function getMediaFilePath (mixed $media, string $version = FileDocument::VERSION_HEAD, bool $absolute = false, bool $preview = false) : string
    {
        if (!$media instanceof MediaDocument) {
            $media = $this->mediaManager->findOneById($media);
        }

        if (!$file = $media->getFileVersion($version)) {
            return '';
        }

        $filename = $file->getPath() ?? '';

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

        return '/' . \ltrim(
            \sprintf(
                '/%s/media/%s/%s',
                $absolute ? $this->configuration->getBaseUploadPath() : 'uploads',
                $this->filesystemHelper->filesystemize((string) $media->getId()),
                $filename,
            ),
            '/',
        );
    }
}
