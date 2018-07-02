<?php

namespace C201\MediaBundle\Model\Storage\PostProcessor;

class PostProcessorPool implements \IteratorAggregate
{
    protected $postProcessors = [];

    /**
     * @param string                       $key
     * @param PostProcessorInterface $postProcessor
     */
    public function addPostProcessor($key, PostProcessorInterface $postProcessor)
    {
        $this->postProcessors[$key] = $postProcessor;
    }

    /**
     * @param $key
     *
     * @return PostProcessorInterface
     */
    public function getPostProcessor($key)
    {
        if (!isset($this->postProcessors[$key])) {
            $message = sprintf('PostProcessor for key "%s" not available.', $key);
            throw new \OutOfBoundsException($message);
        }

        return $this->postProcessors[$key];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->postProcessors);
    }
}
