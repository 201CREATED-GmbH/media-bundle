<?php

namespace C201\MediaBundle\Entity\Listener;

use C201\MediaBundle\Model\Object\ObjectField;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

class MediaStoreListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->handleUploads($args->getObject());
    }

    public function postPersist(LifecycleEventArgs $args)
    {
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        if ($this->handleUploads($args->getObject())) {
            $this->recomputeEntityChange($args);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
    }

    /**
     * Doctrine does not update entities when there are values set in event listeners
     * This method aims to do this manually
     *
     * @param LifecycleEventArgs|\Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    protected function recomputeEntityChange(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $uow = $om->getUnitOfWork();
        if (method_exists($uow, 'recomputeSingleEntityChangeSet')) {
            $uow->recomputeSingleEntityChangeSet(
                $om->getClassMetadata(get_class($args->getEntity())),
                $args->getEntity()
            );
        }
        else {
            $uow->recomputeSingleDocumentChangeSet(
                $om->getClassMetadata(get_class($args->getDocument())),
                $args->getDocument()
            );
        }
    }

    /**
     * Uploads files
     *
     * @param Object $object
     *
     * @return bool
     */
    protected function handleUploads($object)
    {
        $atLeastOneUpload = false;


        $fields = $this->container->get('c201_media.configuration')->getFieldsUnderControl($object);
        foreach ($fields as $fieldName) {
            // if file has an uploaded file, change filename
            $objectField = new ObjectField($object, $fieldName);

            // we assume a new file upload
            if ($objectField->getValue() instanceof File) {
                $this->container->get('c201_media.media')->store($objectField, $objectField->getValue());

                $atLeastOneUpload = true;
            }
        }

        return $atLeastOneUpload;
    }
}
