<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage\PostProcessor;

class PostProcessorPool implements \IteratorAggregate
{
    protected $postProcessors = [];


    public function addPostProcessor (string $key, PostProcessorInterface $postProcessor) : void
    {
        $this->postProcessors[$key] = $postProcessor;
    }


    public function getPostProcessor ($key) : PostProcessorInterface
    {
        if (!isset($this->postProcessors[$key])) {
            $message = \sprintf('PostProcessor for key "%s" not available.', $key);
            throw new \OutOfBoundsException($message);
        }

        return $this->postProcessors[$key];
    }


    #[\ReturnTypeWillChange]
    public function getIterator () : \ArrayIterator
    {
        return new \ArrayIterator($this->postProcessors);
    }
}
