<?php

namespace C201\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="C201\MediaBundle\Repository\DownloadHistoryRepository")
 */
class DownloadHistory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $owner;

    /**
     * @ORM\ManyToOne(targetEntity="C201\MediaBundle\Entity\Media")
     */
    protected $media;

    /**
     * @ORM\Column(type="string")
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $ip;

    /**
     * @ORM\Column(type="string")
     */
    protected $referer;

    /**
     * @ORM\Column(type="string")
     */
    protected $userAgent;

    /**
     * @ORM\Column(type="date")
     */
    protected $downloadedAt;

    /**
     * Gets id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets id
     *
     * @param mixed $id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets owner
     *
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets owner
     *
     * @param mixed $owner
     *
     * @return static
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Gets media
     *
     * @return mixed
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets media
     *
     * @param mixed $media
     *
     * @return static
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Gets user
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets user
     *
     * @param mixed $user
     *
     * @return static
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets ip
     *
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Sets ip
     *
     * @param mixed $ip
     *
     * @return static
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Gets referer
     *
     * @return mixed
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Sets referer
     *
     * @param mixed $referer
     *
     * @return static
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Gets userAgent
     *
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Sets userAgent
     *
     * @param mixed $userAgent
     *
     * @return static
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Gets downloadedAt
     *
     * @return mixed
     */
    public function getDownloadedAt()
    {
        return $this->downloadedAt;
    }

    /**
     * Sets downloadedAt
     *
     * @param mixed $downloadedAt
     *
     * @return static
     */
    public function setDownloadedAt($downloadedAt)
    {
        $this->downloadedAt = $downloadedAt;

        return $this;
    }
}
