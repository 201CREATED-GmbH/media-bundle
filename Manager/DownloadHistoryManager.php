<?php declare(strict_types=1);

namespace C201\MediaBundle\Manager;

use C201\MediaBundle\Entity\DownloadHistory;
use C201\MediaBundle\Entity\Media;
use C201\MediaBundle\Repository\DownloadHistoryRepository;

class DownloadHistoryManager
{
    public function __construct(
        protected readonly DownloadHistoryRepository $repository,
    ) {}

    /**
     * @return DownloadHistory[]
     */
    public function findLastForMedia(Media $media, int $quantity = 30) : array
    {
        return $this->repository->findLatestForMedia($media, $quantity);
    }

    /**
     * @return DownloadHistory[]
     */
    public function findLastForOwner(string $owner, int $quantity = 30) : array
    {
        return $this->repository->findLatestForOwner($owner, $quantity);
    }

    public function trackDownload(Media $media, ?string $ip, ?string $referer, ?string $userAgent, mixed $user = null) : void
    {
        $downloadHistory = new DownloadHistory();
        $downloadHistory->setOwner((string) $media->getOwner());
        $downloadHistory->setMedia($media);
        $downloadHistory->setUser(null !== $user ? (string) $user : null);
        $downloadHistory->setIp($ip);
        $downloadHistory->setReferer($referer);
        $downloadHistory->setUserAgent($userAgent);
        $downloadHistory->setDownloadedAt(new \DateTime('now'));

        $this->repository->save($downloadHistory);
    }
}
