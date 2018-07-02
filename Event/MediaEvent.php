<?php

namespace C201\MediaBundle\Event;

use C201\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class MediaEvent extends Event
{
    /**
     * @var Media
     */
    private $media;
    /**
     * @var
     */
    private $version;
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param Media         $media
     * @param               $version
     * @param UserInterface $user
     */
    public function __construct(Media $media, $version = null, UserInterface $user = null)
    {
        $this->media = $media;
        $this->version = $version;
        $this->user = $user;
    }

    /**
     * Gets media
     *
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Gets version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Gets user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
