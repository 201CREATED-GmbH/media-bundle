<?php

namespace C201\MediaBundle\Model\Configuration;

class ObjectFieldConfiguration
{
    /**
     * @var ObjectConfiguration
     */
    private $objectConfiguration;

    /**
     * @var array
     */
    private $options;

    /**
     * @param ObjectConfiguration $objectConfiguration
     * @param array               $options
     */
    public function __construct(ObjectConfiguration $objectConfiguration, array $options = [])
    {
        $this->objectConfiguration = $objectConfiguration;
        $this->options = $options;
    }

    /**
     * Gets uploadPath
     *
     * @return string
     */
    public function getUploadPath()
    {
        return $this->objectConfiguration->getUploadPath();
    }

    /**
     * @return string
     */
    public function getLiipImagineFilters()
    {
        return $this->options['liip_imagine_filters'];
    }

    /**
     * @return string
     */
    public function getStorage()
    {
        return $this->options['storage']['backend'];
    }

    /**
     * @return array
     */
    public function getStorageOptions()
    {
        return $this->options['storage']['options'];
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    public function getStorageOption($key, $default = null)
    {
        if (!isset($this->options['storage']['options'][$key])) {
            return $default;
        }

        return $this->options['storage']['options'][$key];
    }

    /**
     * Retrieves the constraints available
     *
     * @return array
     */
    public function getConstraints()
    {
        $constraints = [];

        foreach ($this->options['constraints'] as $constraintClass => $constraintOptions) {
            $constraints[] = new $constraintClass($constraintOptions['options']);
        }

        return $constraints;
    }
}
