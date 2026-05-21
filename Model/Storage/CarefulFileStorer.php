<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Model\Configuration\MediaConfiguration;
use C201\MediaBundle\Model\Storage\PostProcessor\PostProcessorPool;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Saves an uploaded file to a directory without overwriting an existing file.
 */
class CarefulFileStorer
{
    public function __construct (
        private readonly MediaConfiguration $configuration,
        private readonly PostProcessorPool $postProcessors,
    )
    {
    }


    public function store (File $file, string $path) : string
    {
        $discriminator = 0;

        do {
            $filename = $this->polishFileName($file, $discriminator++);
            $absoluteFilePath = $this->configuration->getBaseUploadPath() . '/' . $path . '/' . $filename;
        } while (\file_exists($absoluteFilePath));

        $file = $file->move($this->configuration->getBaseUploadPath() . '/' . $path, $filename);

        foreach ($this->postProcessors as $postProcessor) {
            $postProcessor->process($file);
        }

        return $filename;
    }


    protected function polishFileName (File $file, int $discriminator = 0) : string
    {
        if ($file instanceof UploadedFile) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
        }
        else {
            $filename = $file->getBasename();
            $extension = $file->getExtension();
        }

        $filename = \preg_replace('@[^a-z0-9_.-]+@i', '', $filename) ?? $filename;

        if ($discriminator) {
            $filename = \str_replace('.' . $extension, \sprintf('_%d.%s', $discriminator, $extension), $filename);
        }

        return $filename;
    }
}
