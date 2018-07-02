<?php

namespace C201\MediaBundle\Model;

class FilesystemHelper
{
    /**
     * Helps to create folder directories starting with "ab/cd/abcdabcdefg"
     *
     * So it will create subfolders to not get into trouble with too many directories in folders.
     *
     * @param $string
     *
     * @return string
     */
    public function filesystemize($string)
    {
        return sprintf('%s/%s/%s', substr($string, 0, 2), substr($string, 2, 2), $string);
    }
}
