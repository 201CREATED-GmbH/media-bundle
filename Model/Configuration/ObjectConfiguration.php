<?php

namespace C201\MediaBundle\Model\Configuration;

use C201\MediaBundle\Model\Exception\NoObjectFieldConfigurationAvailableException;

class ObjectConfiguration
{
    /**
     * @var array
     */
    private $options;


    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Gets uploadPath
     *
     * @return mixed
     */
    public function getUploadPath()
    {
        return $this->options['upload_path'];
    }

    /**
     * @param $field
     *
     * @return ObjectFieldConfiguration
     *
     * @throws NoObjectFieldConfigurationAvailableException
     */
    public function getFieldConfiguration($field)
    {
        if (!isset($this->options['medias'][$field])) {
            $message = sprintf('Field "%s" is not configured for MediaBundle', $field);
            throw new NoObjectFieldConfigurationAvailableException($message);
        }

        return new ObjectFieldConfiguration($this, $this->options['medias'][$field]);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->options['medias']);
    }
}
