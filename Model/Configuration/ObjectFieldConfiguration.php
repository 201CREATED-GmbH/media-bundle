<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Configuration;

class ObjectFieldConfiguration
{
    public function __construct (
        private readonly ObjectConfiguration $objectConfiguration,
        private readonly array $options = [],
    )
    {
    }


    public function getUploadPath () : string
    {
        return $this->objectConfiguration->getUploadPath();
    }


    public function getLiipImagineFilters () : array
    {
        return $this->options['liip_imagine_filters'];
    }


    public function getStorage () : string
    {
        return $this->options['storage']['backend'];
    }


    public function getStorageOptions () : array
    {
        return $this->options['storage']['options'];
    }


    public function getStorageOption (string $key, mixed $default = null) : mixed
    {
        return $this->options['storage']['options'][$key] ?? $default;
    }


    public function getConstraints () : array
    {
        $constraints = [];

        foreach ($this->options['constraints'] as $constraintClass => $constraintOptions) {
            $constraints[] = new $constraintClass($constraintOptions['options']);
        }

        return $constraints;
    }
}
