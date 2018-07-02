<?php

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Model\Configuration\MediaConfiguration;
use C201\MediaBundle\Model\Storage\PostProcessor\PostProcessorPool;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Saves an uploaded file to a directory but does not overwrite another file
 */
class CarefulFileStorer
{
    /**
     * @var PostProcessorPool
     */
    private $postProcessors;

    /**
     * @var MediaConfiguration
     */
    private $configuration;

    /**
     * @param MediaConfiguration $configuration
     * @param PostProcessorPool  $postProcessors
     */
    public function __construct(MediaConfiguration $configuration, PostProcessorPool $postProcessors)
    {
        $this->postProcessors = $postProcessors;
        $this->configuration = $configuration;
    }

    /**
     * @param File   $file
     * @param string $path
     *
     * @return string
     */
    public function store(File $file, $path)
    {
        $filename = '';
        $discriminator = 0;
        do {
            $filename = $this->polishFileName($file, $discriminator++);
            $absoluteFilePath = $this->configuration->getBaseUploadPath().'/'.$path.'/'.$filename;
        } while (file_exists($absoluteFilePath));

        $file = $file->move($this->configuration->getBaseUploadPath().'/'.$path, $filename);

        foreach ($this->postProcessors as $postProcessor) {
            $postProcessor->process($file);
        }

        return $filename;
    }

    /**
     * @param File $file
     * @param int  $discriminator
     *
     * @return mixed
     */
    protected function polishFileName(File $file, $discriminator = 0)
    {
        if ($file instanceof UploadedFile) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
        } else {
            $filename = $file->getBasename();
            $extension = $file->getExtension();
        }

        $filename = preg_replace('@[^a-z0-9_.-]+@i', '', $filename);

        if ($discriminator) {
            // replace ".doc" with "_1.doc"
            $filename = str_replace('.'.$extension, sprintf('_%d.%s', $discriminator, $extension), $filename);
        }

        return $filename;
    }
}
