<?php

namespace C201\MediaBundle\Model;

class MediaFunctions
{
    public function filesystemize($path, $amount)
    {
        return $path;
    }

    public function hash($length)
    {
        return substr(md5(uniqid(microtime())), 0, $length);
    }

    /**
     * Normalizes a filename and remove all characters not allowed in a filename
     */
    public function normalizeFilename($filename)
    {
        // replace non letter or digits by _
        $filename = preg_replace('#[^\\pL\d._-]+#u', '_', $filename);

        // transliterate
        if (function_exists('iconv'))
        {
            $filename = iconv('utf-8', 'us-ascii//TRANSLIT', $filename);
        }

        // trim
        $filename = trim($filename, '_');

        // remove unwanted characters
        $filename = preg_replace('#[^_\w.-]+#', '', $filename);

        return $filename;
    }
}
