<?php

namespace C201\MediaBundle\Manager;

use C201\MediaBundle\Entity\File as FileDocument;
use C201\MediaBundle\Entity\Media as MediaDocument;
use C201\MediaBundle\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager
{
    /**
     * @var MediaRepository
     */
    protected $repository;

    /**
     * @param MediaRepository $repository
     */
    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $id
     *
     * @return MediaDocument
     */
    public function findOneById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Checks if the uploaded file exists already
     *
     * @param string $owner
     * @param File   $file
     *
     * @return MediaDocument
     */
    public function findOneByOwnerAndFile($owner, File $file)
    {
        return $this->repository->findMediaByHash($owner, $this->getFileHash($file));
    }

    /**
     * @param $owner
     * @param $id
     *
     * @return MediaDocument
     */
    public function findOneByOwnerAndId($owner, $id)
    {
        return $this->repository->findOneByOwnerAndId($owner, $id);
    }

    /**
     * @param $owner
     * @param $name
     *
     * @return MediaDocument
     */
    public function findOneByOwnerAndName($owner, $name)
    {
        return $this->repository->findOneMediaByName($owner, $name);
    }

    /**
     * @param object $owner
     * @param array  $contexts
     *
     * @return MediaDocument[]
     */
    public function findAllByOwnerAndContext($owner, $contexts = [])
    {
        return $this->repository->findMedias($owner, $contexts);
    }


    /**
     * Creates a media instance which is a container for many file objects
     *
     * @param        $owner
     * @param File   $file
     * @param string $context
     *
     * @return MediaDocument
     */
    public function createMedia($owner, File $file, $context = null)
    {
        $media = new MediaDocument();
        $media->setOwner($this->repository->getOwnerString($owner));
        $media->setCreatedAt(new \DateTime());

        if (null !== $context) {
            $media->setContext($context);
        }

        if ($file instanceof UploadedFile) {
            $media->setName($file->getClientOriginalName());
        } else {
            $media->setName($file->getBasename());
        }

        // generate ID
        $this->repository->save($media);

        return $media;
    }

    /**
     * Add a file to a already existant media file
     *
     * @param File $file
     *
     * @return MediaDocument
     */
    public function addFile(MediaDocument $media, File $file)
    {
        $version = $media->getVersions() + 1;
        $media->setVersions($version);

        $fileD = new FileDocument();
        $fileD->setVersion($version);
        $fileD->setMd5($this->getFileHash($file));
        $fileD->setSize($file->getSize());
        $fileD->setUploadedAt(new \DateTime());

        if ($file instanceof UploadedFile) {
            $fileD->setName($file->getClientOriginalName());
            $fileD->setMime($file->getClientMimeType());
        } else {
            $fileD->setName($file->getBasename());
            $fileD->setMime($file->getMimeType());
        }

        $media->addFile($fileD);

        return $fileD;
    }

    /**
     * @param File $file
     *
     * @return string
     */
    protected function getFileHash(File $file)
    {
        return md5_file($file);
    }

    /**
     * Saves media instance to the database
     *
     * @param MediaDocument $media
     */
    public function save(MediaDocument $media)
    {
        $this->repository->save($media);
    }

    /**
     * Deletes item (save delete)
     *
     * @param MediaDocument $media
     */
    public function delete(MediaDocument $media)
    {
        $media->delete();
        $this->repository->save($media);
    }

    /**
     * Undeletes item (save delete)
     *
     * @param MediaDocument $media
     */
    public function undelete(MediaDocument $media)
    {
        $media->undelete();
        $this->repository->save($media);
    }
}
