<?php

namespace C201\MediaBundle\Twig\Extension;

use C201\MediaBundle\Entity\File;
use C201\MediaBundle\Entity\Media as MediaDocument;
use C201\MediaBundle\Manager\MediaManager;
use C201\MediaBundle\Model\Media as MediaModel;
use C201\MediaBundle\Model\MediaLibrary;
use C201\MediaBundle\Model\Object\ObjectField;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension for displaying Media
 */
class MediaExtension extends AbstractExtension
{
    /**
     * @var MediaModel
     */
    private $media;

    /**
     * @var MediaManager
     */
    private $mediaLibrary;

    /**
     * @var string
     */
    private $webDir;

    /**
     * @param MediaModel $media
     * @param MediaLibrary $mediaLibrary
     * @param string $webDir
     */
    public function __construct(MediaModel $media, MediaLibrary $mediaLibrary, $webDir)
    {
        $this->media = $media;
        $this->mediaLibrary = $mediaLibrary;
        $this->webDir = $webDir;
    }

    /**
     * {@inherited}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('path_media', function ($object, $fieldName, $preview = false) {
                return $this->media->getPath(new ObjectField($object, $fieldName), $preview);
            }),
            new TwigFilter('path_media_root', function ($object, $fieldName, $preview = false) {
                $path = $this->media->getPath(new ObjectField($object, $fieldName), $preview);

                return $this->webDir . '/' . $path;
            }),
            new TwigFilter('media_mime_type', function ($object, $fieldName, $preview = false) {
                $path = $this->webDir . '/' . $this->media->getPath(new ObjectField($object, $fieldName), $preview);

                return mime_content_type($path);
            }),
            new TwigFilter('media_pdf_images', function ($object, $fieldName, $preview = false) {
                $path = $this->webDir . '/' . $this->media->getPath(new ObjectField($object, $fieldName), $preview);

                $pdfImages = [];
                if (mime_content_type($path) == 'application/pdf') {
                    $pdf = new \Spatie\PdfToImage\Pdf($path);
                    foreach (range(1, $pdf->getNumberOfPages()) as $pageNumber) {
                        $pdfImages[] = 'data:image/jpg;base64,' .
                            base64_encode($pdf->setPage($pageNumber)->getImageData('page' . $pageNumber . 'jpg'));
                    }
                }

                return $pdfImages;
            }),
            new TwigFilter('path_media_library', function (MediaDocument $media, $preview = false, $version = File::VERSION_HEAD) {
                return $this->mediaLibrary->getMediaFilePath($media, $version, false, $preview);
            }),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'c201_media';
    }
}
