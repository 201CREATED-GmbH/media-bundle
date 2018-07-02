<?php

namespace C201\MediaBundle\Model\Object;

use Symfony\Component\HttpFoundation\File\File;

class ObjectFileContainer
{
    protected $object;
    protected $file;

    /**
     * @param      $object
     * @param File $file
     */
    public function __construct($object, File $file = null)
    {
        $this->object = $object;
        $this->file   = $file;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getFile()
    {
        return $this->file;
    }
}
