<?php declare(strict_types=1);

namespace C201\MediaBundle\Model;

class FilesystemHelper
{
    /**
     * Creates a subdirectory path like "ab/cd/abcdabcdefg" to avoid too many entries in a single folder.
     */
    public function filesystemize (string $string) : string
    {
        return \sprintf('%s/%s/%s', \substr($string, 0, 2), \substr($string, 2, 2), $string);
    }
}
