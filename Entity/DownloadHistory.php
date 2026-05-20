<?php

namespace C201\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \C201\MediaBundle\Repository\DownloadHistoryRepository::class)]
class DownloadHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string')]
    protected $owner;

    #[ORM\ManyToOne(targetEntity: \C201\MediaBundle\Entity\Media::class)]
    protected $media;

    #[ORM\Column(type: 'string')]
    protected $user;

    #[ORM\Column(type: 'string')]
    protected $ip;

    #[ORM\Column(type: 'string')]
    protected $referer;

    #[ORM\Column(type: 'string')]
    protected $userAgent;

    #[ORM\Column(type: 'date')]
    protected $downloadedAt;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; return $this; }
    public function getOwner() { return $this->owner; }
    public function setOwner($owner) { $this->owner = $owner; return $this; }
    public function getMedia() { return $this->media; }
    public function setMedia($media) { $this->media = $media; return $this; }
    public function getUser() { return $this->user; }
    public function setUser($user) { $this->user = $user; return $this; }
    public function getIp() { return $this->ip; }
    public function setIp($ip) { $this->ip = $ip; return $this; }
    public function getReferer() { return $this->referer; }
    public function setReferer($referer) { $this->referer = $referer; return $this; }
    public function getUserAgent() { return $this->userAgent; }
    public function setUserAgent($userAgent) { $this->userAgent = $userAgent; return $this; }
    public function getDownloadedAt() { return $this->downloadedAt; }
    public function setDownloadedAt($downloadedAt) { $this->downloadedAt = $downloadedAt; return $this; }
}
