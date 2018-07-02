<?php

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Model\MediaFunctions;
use C201\MediaBundle\Model\Object\ObjectFileContainer;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PathParser
{
    protected $functions;

    public function __construct(MediaFunctions $functions)
    {
        $this->functions = $functions;
    }

    public function parse($string, ObjectFileContainer $container, $discriminator = '')
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        // fetch all "{ ... }"
        $filename = preg_replace_callback('@{(.*?)}@', function ($match) use ($container, $accessor) {
            // replace all variables starting with "entity." or "file."
            $string = preg_replace_callback('@((object|file)(\.[a-zA-Z0-9]+)+)@', function ($match) use ($container, $accessor) {
                return $accessor->getValue($container, $match[1]);
            }, $match[1]);

            // replace all method calls
            $string = preg_replace_callback('@([a-zA-Z0-9]+)\((.*?)\)@', function ($match) {
                $method    = $match[1];
                $arguments = preg_split('@\s*,\s*@', $match[2]);
                $callable = array($this->functions, $method);
                if (!is_callable($callable)) {
                    throw new \InvalidArgumentException(sprintf('Method "%s" not callable with arguments "%s".', $method, $match[2]));
                }
                return call_user_func_array($callable, $arguments);
            }, $string);

            return $string;
        }, $string);

        if ($discriminator) {
            // when discriminator is set to "1" filename will transform in this way:
            //  file.pdf -> file_1.pdf
            $filename = preg_replace('@(\.[^.]+)$@', '_'.$discriminator.'\\1', $filename);
        }

        return $filename;
    }
}
