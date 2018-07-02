<?php

namespace C201\MediaBundle\EventListener;

use C201\MediaBundle\Entity\AttachmentInterface;
use C201\MediaBundle\Model\Configuration\MediaConfiguration;
use C201\MediaBundle\Model\Configuration\ObjectFieldConfiguration;
use C201\MediaBundle\Model\Media;
use C201\MediaBundle\Model\Object\ObjectField;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MediaSerializationSubscriber implements \JMS\Serializer\EventDispatcher\EventSubscriberInterface
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var MediaConfiguration
     */
    private $mediaConfiguration;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * MediaSerializationSubscriber constructor.
     *
     * @param Media              $media
     * @param MediaConfiguration $mediaConfiguration
     * @param CacheManager       $cacheManager
     */
    public function __construct(Media $media, MediaConfiguration $mediaConfiguration, CacheManager $cacheManager = null)
    {
        $this->media  = $media;
        $this->mediaConfiguration = $mediaConfiguration;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event'  => 'serializer.pre_serialize',
                'method' => 'onPreSerialize',
            ],
        ];
    }

    /**
     * @param PreSerializeEvent $event
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();

        if (!$fields = $this->mediaConfiguration->getFieldsUnderControl($object)) {
            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($fields as $field) {
            if (!$propertyAccessor->getValue($object, $field)) {
                continue;
            }

            $objectField = new ObjectField($object, $field);
            $objectFieldConfiguration = $this->mediaConfiguration->getObjectFieldConfiguration($objectField);
            $normalizedPath = $this->media->getPath($objectField);

            // if no liip imagine filters are defined, only set value
            if (null === $this->cacheManager || !$objectFieldConfiguration->getLiipImagineFilters()) {
                $propertyAccessor->setValue($object, $field, $normalizedPath);
            } else {
                // otherwise check for AttachmentInterface
                if ($object instanceof AttachmentInterface) {
                    // ... there we can determine if current object has an image related to it where to apply
                    if ($object->getIsImage()) {
                        $propertyAccessor->setValue($object, $field, $this->applyLiipImagineFilters($objectFieldConfiguration, $normalizedPath));
                    }
                    else {
                        $propertyAccessor->setValue($object, $field, $normalizedPath);
                    }
                } else {
                    $propertyAccessor->setValue($object, $field, $this->applyLiipImagineFilters($objectFieldConfiguration, $normalizedPath));
                }
            }
        }
    }

    /**
     * @param $objectFieldConfiguration
     * @param $normalizedPath
     *
     * @return array
     */
    protected function applyLiipImagineFilters(ObjectFieldConfiguration $objectFieldConfiguration, $normalizedPath)
    {
        $configs = [];
        foreach ($objectFieldConfiguration->getLiipImagineFilters() as $serializationKey) {
            $configs[$serializationKey] = $this->cacheManager->getBrowserPath($normalizedPath, $serializationKey);
        }

        return $configs;
    }
}
