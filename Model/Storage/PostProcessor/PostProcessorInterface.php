<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage\PostProcessor;

interface PostProcessorInterface
{
    public function process (\SplFileInfo $file);
}
