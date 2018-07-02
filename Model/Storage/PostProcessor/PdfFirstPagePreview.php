<?php

namespace C201\MediaBundle\Model\Storage\PostProcessor;

class PdfFirstPagePreview implements PostProcessorInterface
{
    /**
     * @param \SplFileInfo $file
     */
    public function process(\SplFileInfo $file)
    {
        if ('pdf' !== strtolower($file->getExtension())) {
            return;
        }

        $imagick = new \Imagick();
        $imagick->readImage($file.'[0]');
        $imagick->setImageFormat('png');
        $imagick->writeImage(sprintf('%s.png', $file));
        $imagick->clear();
    }
}
