<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Entity\AttachmentInterface;
use C201\MediaBundle\Model\Configuration\ObjectFieldConfiguration;
use C201\MediaBundle\Model\Object\ObjectField;
use C201\MediaBundle\Model\Object\ObjectFileContainer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StorageBackendFile implements StorageBackendInterface
{
    public function __construct (
        private readonly PathParser $pathParser,
        private readonly CarefulFileStorer $storer,
    )
    {
    }


    public function store (ObjectFieldConfiguration $config, ObjectField $objectField, File $file) : bool
    {
        $path = $this->pathParser->parse($config->getUploadPath(), new ObjectFileContainer($objectField->getObject(), $file));
        $fileName = $this->storer->store($file, $path);
        $objectField->setValue($fileName);

        $object = $objectField->getObject();

        if ($object instanceof AttachmentInterface) {
            if ($file instanceof UploadedFile) {
                $object->setMimeType($file->getClientMimeType());
                $object->setOriginalName($file->getClientOriginalName());
                $object->setSize($file->getSize());
            }
            else {
                $object->setMimeType($file->getMimeType());
                $object->setOriginalName($file->getFilename());
                $object->setSize($file->getSize());
            }
            $object->setIsImage(\str_contains(\strtolower((string) $object->getMimeType()), 'image'));
        }

        return true;
    }


    public function getRelativePath (ObjectFieldConfiguration $config, ObjectField $objectField, bool $preview = false) : string
    {
        $filename = (string) $objectField->getValue();

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

        return \sprintf(
            '/uploads/%s/%s',
            $this->pathParser->parse($config->getUploadPath(), new ObjectFileContainer($objectField->getObject())),
            \rawurlencode($filename),
        );
    }
}
