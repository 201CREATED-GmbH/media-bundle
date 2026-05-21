<?php declare(strict_types=1);

namespace C201\MediaBundle\Manager;

use C201\MediaBundle\Entity\File as FileDocument;
use C201\MediaBundle\Entity\Media as MediaDocument;
use C201\MediaBundle\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager
{
    public function __construct(
        protected readonly MediaRepository $repository,
    ) {}

    public function findOneById(mixed $id) : ?MediaDocument
    {
        return $this->repository->find($id);
    }

    public function findOneByOwnerAndFile(mixed $owner, File $file) : ?MediaDocument
    {
        return $this->repository->findMediaByHash($owner, $this->getFileHash($file));
    }

    public function findOneByOwnerAndId(mixed $owner, mixed $id) : ?MediaDocument
    {
        return $this->repository->findOneByOwnerAndId($owner, $id);
    }

    public function findOneByOwnerAndName(mixed $owner, string $name) : ?MediaDocument
    {
        return $this->repository->findOneMediaByName($owner, $name);
    }

    /**
     * @return MediaDocument[]
     */
    public function findAllByOwnerAndContext(mixed $owner, array $contexts = []) : array
    {
        return $this->repository->findMedias($owner, $contexts);
    }

    public function createMedia(mixed $owner, File $file, ?string $context = null) : MediaDocument
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

        $this->repository->save($media);

        return $media;
    }

    public function addFile(MediaDocument $media, File $file) : FileDocument
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
            $fileD->setMime($file->getMimeType() ?? '');
        }

        $media->addFile($fileD);

        return $fileD;
    }

    protected function getFileHash(File $file) : string
    {
        return \md5_file($file->getPathname()) ?: '';
    }

    public function save(MediaDocument $media) : void
    {
        $this->repository->save($media);
    }

    public function delete(MediaDocument $media) : void
    {
        $media->delete();
        $this->repository->save($media);
    }

    public function undelete(MediaDocument $media) : void
    {
        $media->undelete();
        $this->repository->save($media);
    }
}
