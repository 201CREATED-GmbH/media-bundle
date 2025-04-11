<?php

namespace C201\MediaBundle\Model\Storage\PostProcessor;

use ReturnTypeWillChange;

class PostProcessorPool implements \IteratorAggregate
{
    protected $postProcessors = [];


    public function addPostProcessor (string $key, PostProcessorInterface $postProcessor)
    {
        $this->postProcessors[$key] = $postProcessor;
    }


    public function getPostProcessor ($key) : PostProcessorInterface
    {
        if (!isset($this->postProcessors[$key])) {
            $message = sprintf('PostProcessor for key "%s" not available.', $key);
            throw new \OutOfBoundsException($message);
        }

        return $this->postProcessors[$key];
    }


    #[ReturnTypeWillChange] public function getIterator () : \ArrayIterator
    {
        return new \ArrayIterator($this->postProcessors);
    }
}
