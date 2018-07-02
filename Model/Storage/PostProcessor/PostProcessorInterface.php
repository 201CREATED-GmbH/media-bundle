<?php

namespace C201\MediaBundle\Model\Storage\PostProcessor;

interface PostProcessorInterface
{
    public function process(\SplFileInfo $file);
}
