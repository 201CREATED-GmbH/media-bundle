<?php declare(strict_types=1);

namespace C201\MediaBundle\Entity;

use C201\MediaBundle\Repository\DownloadHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DownloadHistoryRepository::class)]
class DownloadHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string')]
    protected ?string $owner = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    protected ?Media $media = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $user = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $ip = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $referer = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $userAgent = null;

    #[ORM\Column(type: 'date')]
    protected ?\DateTime $downloadedAt = null;


    public function getId () : ?int
    {
        return $this->id;
    }


    public function getOwner () : ?string
    {
        return $this->owner;
    }


    public function setOwner (string $owner) : static
    {
        $this->owner = $owner;
        return $this;
    }


    public function getMedia () : ?Media
    {
        return $this->media;
    }


    public function setMedia (?Media $media) : static
    {
        $this->media = $media;
        return $this;
    }


    public function getUser () : ?string
    {
        return $this->user;
    }


    public function setUser (?string $user) : static
    {
        $this->user = $user;
        return $this;
    }


    public function getIp () : ?string
    {
        return $this->ip;
    }


    public function setIp (?string $ip) : static
    {
        $this->ip = $ip;
        return $this;
    }


    public function getReferer () : ?string
    {
        return $this->referer;
    }


    public function setReferer (?string $referer) : static
    {
        $this->referer = $referer;
        return $this;
    }


    public function getUserAgent () : ?string
    {
        return $this->userAgent;
    }


    public function setUserAgent (?string $userAgent) : static
    {
        $this->userAgent = $userAgent;
        return $this;
    }


    public function getDownloadedAt () : ?\DateTime
    {
        return $this->downloadedAt;
    }


    public function setDownloadedAt (\DateTime $downloadedAt) : static
    {
        $this->downloadedAt = $downloadedAt;
        return $this;
    }
}
