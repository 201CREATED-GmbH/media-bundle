<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Configuration;

use C201\MediaBundle\Model\Exception\NoObjectFieldConfigurationAvailableException;

class ObjectConfiguration
{
    public function __construct (
        private readonly array $options = [],
    )
    {
    }


    public function getUploadPath () : string
    {
        return $this->options['upload_path'];
    }


    /**
     * @throws NoObjectFieldConfigurationAvailableException
     */
    public function getFieldConfiguration (string $field) : ObjectFieldConfiguration
    {
        if (!isset($this->options['medias'][$field])) {
            throw new NoObjectFieldConfigurationAvailableException(
                \sprintf('Field "%s" is not configured for MediaBundle', $field)
            );
        }

        return new ObjectFieldConfiguration($this, $this->options['medias'][$field]);
    }


    /**
     * @return string[]
     */
    public function getFields () : array
    {
        return \array_keys($this->options['medias']);
    }
}
