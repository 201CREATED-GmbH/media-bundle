<?php declare(strict_types=1);

namespace C201\MediaBundle\Repository;

use C201\MediaBundle\Entity\DownloadHistory;
use C201\MediaBundle\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DownloadHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DownloadHistory::class);
    }

    /**
     * @return DownloadHistory[]
     */
    public function findLatestForOwner(string $owner, int $quantity = 30) : array
    {
        return $this->createQueryBuilder('dh')
            ->where('dh.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('dh.id', 'desc')
            ->setMaxResults($quantity)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DownloadHistory[]
     */
    public function findLatestForMedia(Media $media, int $quantity = 30) : array
    {
        return $this->createQueryBuilder('dh')
            ->where('dh.media = :media')
            ->setParameter('media', $media)
            ->orderBy('dh.id', 'desc')
            ->setMaxResults($quantity)
            ->getQuery()
            ->getResult();
    }

    public function save(DownloadHistory $downloadHistory, bool $andFlush = true) : void
    {
        $em = $this->getEntityManager();
        $em->persist($downloadHistory);

        if ($andFlush) {
            $em->flush();
        }
    }
}
