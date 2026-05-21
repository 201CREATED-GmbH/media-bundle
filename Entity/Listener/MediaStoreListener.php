<?php declare(strict_types=1);

namespace C201\MediaBundle\Entity\Listener;

use C201\MediaBundle\Model\Object\ObjectField;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

class MediaStoreListener
{
    public function __construct (
        private readonly ContainerInterface $container,
    )
    {
    }


    public function prePersist (LifecycleEventArgs $args) : void
    {
        $this->handleUploads($args->getObject());
    }


    public function postPersist (LifecycleEventArgs $args) : void
    {
    }


    public function preUpdate (LifecycleEventArgs $args) : void
    {
        if ($this->handleUploads($args->getObject())) {
            $this->recomputeEntityChange($args);
        }
    }


    public function postUpdate (LifecycleEventArgs $args) : void
    {
    }


    /**
     * Doctrine does not update entities when values are set in event listeners.
     * This method recomputes the changeset manually.
     */
    protected function recomputeEntityChange (LifecycleEventArgs $args) : void
    {
        $om = $args->getObjectManager();

        $object = $args->getObject();
        $om->getUnitOfWork()->recomputeSingleEntityChangeSet(
            $om->getClassMetadata($object::class),
            $object,
        );
    }


    protected function handleUploads (object $object) : bool
    {
        $atLeastOneUpload = false;

        $fields = $this->container->get('c201_media.configuration')->getFieldsUnderControl($object);

        foreach ($fields as $fieldName) {
            $objectField = new ObjectField($object, $fieldName);

            if ($objectField->getValue() instanceof File) {
                $this->container->get('c201_media.media')->store($objectField, $objectField->getValue());
                $atLeastOneUpload = true;
            }
        }

        return $atLeastOneUpload;
    }
}
