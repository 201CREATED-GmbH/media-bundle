<?php declare(strict_types=1);

namespace C201\MediaBundle\Twig\Extension;

use C201\MediaBundle\Entity\File;
use C201\MediaBundle\Entity\Media as MediaDocument;
use C201\MediaBundle\Model\Media as MediaModel;
use C201\MediaBundle\Model\MediaLibrary;
use C201\MediaBundle\Model\Object\ObjectField;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MediaExtension extends AbstractExtension
{
    public function __construct (
        private readonly MediaModel $media,
        private readonly MediaLibrary $mediaLibrary,
        private readonly string $webDir,
    )
    {
    }


    public function getFilters () : array
    {
        return [
            new TwigFilter('path_media', function (mixed $object, string $fieldName, bool $preview = false) : string {
                return $this->media->getPath(new ObjectField($object, $fieldName), $preview);
            }),
            new TwigFilter('path_media_root', function (mixed $object, string $fieldName, bool $preview = false) : string {
                $path = $this->media->getPath(new ObjectField($object, $fieldName), $preview);
                return $this->webDir . '/' . $path;
            }),
            new TwigFilter('media_mime_type', function (mixed $object, string $fieldName, bool $preview = false) : string {
                $path = $this->webDir . '/' . $this->media->getPath(new ObjectField($object, $fieldName), $preview);
                return \mime_content_type($path) ?: '';
            }),
            new TwigFilter('path_media_library', function (MediaDocument $media, bool $preview = false, string $version = File::VERSION_HEAD) : string {
                return $this->mediaLibrary->getMediaFilePath($media, $version, false, $preview);
            }),
        ];
    }
}
