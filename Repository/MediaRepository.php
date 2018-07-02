<?php

namespace C201\MediaBundle\Repository;

use C201\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityRepository;

class MediaRepository extends EntityRepository
{
    /**
     * Retrieves for a select list all the elements
     *
     * @param $owner
     * @param $context
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryBuilderForOwnerAndContext($owner, $context)
    {
        return $this
            ->createQueryBuilder('m')
            ->andWhere('m.context = :context')
            ->andWhere('m.owner = :owner')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameters([
                'context' => $context,
                'owner'   => $owner,
            ])
            ->orderBy('id', 'desc')
        ;
    }

    /**
     * Checks if a file was uploaded already
     *
     * @param $owner
     * @param $hash
     *
     * @return Media
     */
    public function findMediaByHash($owner, $hash)
    {
        return $this
            ->createQueryBuilder()
            ->field('owner')->equals($this->getOwnerString($owner))
            ->field('files.md5')->equals($hash)
            ->getQuery()
            ->getSingleResult()
            ;
    }


    /**
     * @param $ownerObject
     *
     * @return string
     */
    public function getOwnerString($ownerObject)
    {
        if (is_object($ownerObject) && method_exists($ownerObject, 'getId')) {
            $ownerObject = sprintf('%s-%s', get_class($ownerObject), $ownerObject->getId());
            $ownerObject = str_replace('MongoDBODMProxies\\__CG__\\', '', $ownerObject);
        }


        return (string) $ownerObject;
    }

    /**
     * Finds a single Media by its id
     *
     * @param  $id
     *
     * @return Media
     */
    public function findOneByOwnerAndId($owner, $id)
    {
        return $this->findOneBy(
            [
                'owner' => $this->getOwnerString($owner),
                'id'    => $id,
            ]
        );
    }

    /**
     * @param object $owner
     * @param string $name
     *
     * @return Media
     */
    public function findOneMediaByName($owner, $name)
    {
        return $this->findOneBy(
            [
                'owner' => $this->getOwnerString($owner),
                'name'  => $name,
            ]
        );
    }


    /**
     * Finds all medias
     *
     * @param string   $owner
     * @param string[] $contexts
     *
     * @return mixed
     */
    public function findMedias($owner, $contexts = [])
    {
        $qb = $this
            ->createQueryBuilder()
            // select only the latest uploaded file
            ->selectSlice('files', -1);

        if ($contexts) {
            $qb->field('context')->in($contexts);
        }

        $qb
            ->field('owner')->equals($this->getOwnerString($owner))
            ->field('deletedAt')->equals(null)
            ->sort('_id', 'desc')
        ;

        return $qb
            ->getQuery()
            ->execute();
    }

    /**
     * @param Media $media
     * @param bool  $andFlush
     */
    public function save(Media $media, $andFlush = true)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($media);

        if ($andFlush) {
            $entityManager->flush();
        }
    }

    /**
     * @param Media $media
     * @param bool  $andFlush
     */
    public function remove(Media $media, $andFlush = true)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($media);

        if ($andFlush) {
            $entityManager->flush();
        }
    }
}
