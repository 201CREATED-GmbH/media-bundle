<?php

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Entity\AttachmentInterface;
use C201\MediaBundle\Model\Configuration\ObjectFieldConfiguration;
use C201\MediaBundle\Model\Object\ObjectField;
use C201\MediaBundle\Model\Object\ObjectFileContainer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StorageBackendFile implements StorageBackendInterface
{
    /**
     * @var PathParser
     */
    private $pathParser;

    /**
     * @var CarefulFileStorer
     */
    private $storer;

    /**
     * @param PathParser                $pathParser
     * @param CarefulFileStorer $storer
     */
    public function __construct(PathParser $pathParser, CarefulFileStorer $storer)
    {
        $this->pathParser = $pathParser;
        $this->storer = $storer;
    }

    /**
     * @param ObjectFieldConfiguration $config
     * @param ObjectField              $objectField
     * @param File             $file
     *
     * @return bool
     */
    public function store(ObjectFieldConfiguration $config, ObjectField $objectField, File $file)
    {
        $path = $this->pathParser->parse($config->getUploadPath(), new ObjectFileContainer($objectField->getObject(), $file));

        $fileName = $this->storer->store($file, $path);

        $objectField->setValue($fileName);

        // store attachment data to file
        $object = $objectField->getObject();
        if ($object instanceof AttachmentInterface) {
            if ($file instanceof UploadedFile) {
                $object->setMimeType($file->getClientMimeType());
                $object->setOriginalName($file->getClientOriginalName());
                $object->setSize($file->getClientSize());
            } else {
                $object->setMimeType($file->getMimeType());
                $object->setOriginalName($file->getFilename());
                $object->setSize($file->getSize());
            }
            $object->setIsImage(strpos(strtolower($object->getMimeType()), 'image') !== false);
        }

        return true;
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
        $filename = $objectField->getValue();

        if ($preview) {
            if (preg_match('@\.(pdf)$@i', $filename)) {
                $filename .= '.png';
            }
            elseif (preg_match('@\.(jpe?g|png)$@i', $filename)) {
                // do nothing, not previewable
            }
            else {
                return '';
            }
        }

        return sprintf(
            '/uploads/%s/%s',
            $this->pathParser->parse($config->getUploadPath(), new ObjectFileContainer($objectField->getObject())),
            $filename
        );
    }
}
