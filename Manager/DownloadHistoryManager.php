<?php

namespace C201\MediaBundle\Manager;

use C201\MediaBundle\Entity\DownloadHistory;
use C201\MediaBundle\Entity\Media;
use C201\MediaBundle\Repository\DownloadHistoryRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class DownloadHistoryManager
{
    /**
     * @var DownloadHistoryRepository
     */
    protected $repository;

    /**
     * @param DownloadHistoryRepository $repository
     */
    public function __construct(DownloadHistoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Media   $media
     * @param integer $quantity
     *
     * @return DownloadHistory[]
     */
    public function findLastForMedia(Media $media, $quantity)
    {
        return $this->repository->findLatestForMedia($media, $quantity);
    }

    /**
     * @param string  $owner
     * @param integer $quantity
     *
     * @return DownloadHistory[]
     */
    public function findLastForOwner($owner, $quantity)
    {
        return $this->repository->findLatestForMedia($owner, $quantity);
    }

    /**
     * @param Media  $media
     * @param string $ip
     * @param string $referer
     * @param string $userAgent
     * @param string $user
     */
    public function trackDownload(Media $media, $ip, $referer, $userAgent, $user = null)
    {
        $downloadHistory = new DownloadHistory();
        $downloadHistory->setOwner($media->getOwner());
        $downloadHistory->setMedia($media);
        $downloadHistory->setUser($user);
        $downloadHistory->setIp($ip);
        $downloadHistory->setReferer($referer);
        $downloadHistory->setUserAgent($userAgent);
        $downloadHistory->setDownloadedAt(new \DateTime('now'));

        $this->repository->save($downloadHistory);
    }
}
