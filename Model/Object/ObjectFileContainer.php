<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Object;

use Symfony\Component\HttpFoundation\File\File;

class ObjectFileContainer
{
    protected $object;
    protected $file;


    /**
     */
    public function __construct ($object, ?File $file = null)
    {
        $this->object = $object;
        $this->file = $file;
    }


    /**
     * @return mixed
     */
    public function getObject ()
    {
        return $this->object;
    }


    /**
     * @return File
     */
    public function getFile ()
    {
        return $this->file;
    }
}
