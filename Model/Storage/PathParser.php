<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Storage;

use C201\MediaBundle\Model\MediaFunctions;
use C201\MediaBundle\Model\Object\ObjectFileContainer;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PathParser
{
    public function __construct (
        protected readonly MediaFunctions $functions,
    )
    {
    }


    public function parse (string $string, ObjectFileContainer $container, string $discriminator = '') : string
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $filename = \preg_replace_callback('@{(.*?)}@', function (array $match) use ($container, $accessor) : string {
            $string = \preg_replace_callback('@((object|file)(\\.[a-zA-Z0-9]+)+)@', function (array $match) use ($container, $accessor) : string {
                return (string) $accessor->getValue($container, $match[1]);
            }, $match[1]);

            $string = \preg_replace_callback('@([a-zA-Z0-9]+)\\((.*?)\\)@', function (array $match) : string {
                $method = $match[1];
                $arguments = \preg_split('@\\s*,\\s*@', $match[2]) ?: [];
                $callable = [$this->functions, $method];

                if (!\is_callable($callable)) {
                    throw new \InvalidArgumentException(\sprintf('Method "%s" not callable with arguments "%s".', $method, $match[2]));
                }
                return (string) \call_user_func_array($callable, $arguments);
            }, $string ?? '');

            return $string ?? '';
        }, $string) ?? $string;

        if ($discriminator) {
            $filename = \preg_replace('@(\\.[^.]+)$@', '_' . $discriminator . '\\1', $filename) ?? $filename;
        }

        return $filename;
    }
}
