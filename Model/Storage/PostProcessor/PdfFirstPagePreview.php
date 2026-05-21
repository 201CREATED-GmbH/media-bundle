<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage\PostProcessor;

class PdfFirstPagePreview implements PostProcessorInterface
{
    /**
     */
    public function process (\SplFileInfo $file) : void
    {
        if ('pdf' !== \strtolower($file->getExtension())) {
            return;
        }

        $imagick = new \Imagick();
        $imagick->readImage($file . '[0]');
        $imagick->setImageFormat('png');
        $imagick->writeImage(\sprintf('%s.png', $file));
        $imagick->clear();
    }
}
