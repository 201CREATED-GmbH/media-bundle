<?php

namespace C201\MediaBundle\Repository;

use C201\MediaBundle\Entity\DownloadHistory;
use C201\MediaBundle\Entity\Media;
use Doctrine\MongoDB\Query;
use Doctrine\ODM\MongoDB\DocumentRepository;

class DownloadHistoryRepository extends DocumentRepository
{
    /**
     * @param string $owner
     * @param int    $quantity
     *
     * @return DownloadHistory[]
     */
    public function findLatestForOwner($owner, $quantity = 30)
    {
        return $this
            ->createQueryBuilder()
                ->field('owner')->equals($owner)
                ->limit($quantity)
                ->sort('id', 'desc')
            ->getQuery()
            ->execute();
    }

    /**
     * @param Media $media
     * @param int   $quantity
     *
     * @return DownloadHistory[]
     */
    public function findLatestForMedia(Media $media, $quantity = 30)
    {
        return $this
            ->createQueryBuilder()
                ->field('media')->equals($media->getId())
                ->limit($quantity)
                ->sort('id', 'desc')
            ->getQuery()
            ->execute();
    }

    /**
     * @param DownloadHistory $downloadHistory
     * @param bool            $andFlush
     */
    public function save(DownloadHistory $downloadHistory, $andFlush = true)
    {
        $dm = $this->getDocumentManager();
        $dm->persist($downloadHistory);

        if ($andFlush) {
            $dm->flush();
        }
    }
}
