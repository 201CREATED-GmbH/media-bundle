<?php declare(strict_types=1);

namespace C201\MediaBundle\Model\Exception;

use C201\MediaBundle\Entity\Media;

class FileAlreadyExistsException extends \Exception
{
    /**
     * @var Media
     */
    private $media;


    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct (Media $media, $message = '', $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->media = $media;
    }


    /**
     * Gets media
     *
     * @return Media
     */
    public function getMedia ()
    {
        return $this->media;
    }
}
