<?php declare(strict_types=1);

namespace C201\MediaBundle\Event;

use C201\MediaBundle\Entity\Media;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MediaEvent extends Event
{
    public function __construct (
        private readonly Media $media,
        private readonly mixed $version = null,
        private readonly ?UserInterface $user = null,
    )
    {
    }


    public function getMedia () : Media
    {
        return $this->media;
    }


    public function getVersion () : mixed
    {
        return $this->version;
    }


    public function getUser () : ?UserInterface
    {
        return $this->user;
    }
}
