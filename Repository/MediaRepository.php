<?php declare(strict_types=1);

namespace C201\MediaBundle\Repository;

use C201\MediaBundle\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function queryBuilderForOwnerAndContext(mixed $owner, string $context) : QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.context = :context')
            ->andWhere('m.owner = :owner')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameters(new ArrayCollection([
                'context' => $context,
                'owner' => $this->getOwnerString($owner),
            ]))
            ->orderBy('m.id', 'desc');
    }

    public function findMediaByHash(mixed $owner, string $hash) : ?Media
    {
        return $this->createQueryBuilder('m')
            ->join('m.files', 'f')
            ->where('m.owner = :owner')
            ->andWhere('f.md5 = :hash')
            ->setParameter('owner', $this->getOwnerString($owner))
            ->setParameter('hash', $hash)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getOwnerString(mixed $ownerObject) : string
    {
        if (\is_object($ownerObject) && \method_exists($ownerObject, 'getId')) {
            return \sprintf('%s-%s', $ownerObject::class, $ownerObject->getId());
        }

        return (string) $ownerObject;
    }

    public function findOneByOwnerAndId(mixed $owner, mixed $id) : ?Media
    {
        return $this->findOneBy([
            'owner' => $this->getOwnerString($owner),
            'id' => $id,
        ]);
    }

    public function findOneMediaByName(mixed $owner, string $name) : ?Media
    {
        return $this->findOneBy([
            'owner' => $this->getOwnerString($owner),
            'name' => $name,
        ]);
    }

    /**
     * @return Media[]
     */
    public function findMedias(mixed $owner, array $contexts = []) : array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.owner = :owner')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('owner', $this->getOwnerString($owner))
            ->orderBy('m.id', 'desc');

        if ($contexts) {
            $qb->andWhere('m.context IN (:contexts)')
               ->setParameter('contexts', $contexts);
        }

        return $qb->getQuery()->getResult();
    }

    public function save(Media $media, bool $andFlush = true) : void
    {
        $em = $this->getEntityManager();
        $em->persist($media);

        if ($andFlush) {
            $em->flush();
        }
    }

    public function remove(Media $media, bool $andFlush = true) : void
    {
        $em = $this->getEntityManager();
        $em->remove($media);

        if ($andFlush) {
            $em->flush();
        }
    }
}
