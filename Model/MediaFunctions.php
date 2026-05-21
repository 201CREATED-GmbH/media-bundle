<?php declare(strict_types=1);

namespace C201\MediaBundle\Model;

class MediaFunctions
{
    public function filesystemize (string $path, mixed $amount) : string
    {
        return $path;
    }


    public function hash (int $length) : string
    {
        return \substr(\md5(\uniqid((string) \microtime(), true)), 0, $length);
    }


    public function normalizeFilename (string $filename) : string
    {
        $filename = \preg_replace('#[^\\pL\\d._-]+#u', '_', $filename) ?? $filename;

        if (\function_exists('iconv')) {
            $filename = \iconv('utf-8', 'us-ascii//TRANSLIT', $filename) ?: $filename;
        }

        $filename = \trim($filename, '_');
        return \preg_replace('#[^_\\w.-]+#', '', $filename) ?? $filename;
    }
}
