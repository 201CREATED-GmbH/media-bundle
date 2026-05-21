<?php declare(strict_types=1);

namespace C201\MediaBundle\EventListener;

use C201\MediaBundle\Entity\AttachmentInterface;
use C201\MediaBundle\Model\Configuration\MediaConfiguration;
use C201\MediaBundle\Model\Configuration\ObjectFieldConfiguration;
use C201\MediaBundle\Model\Exception\NoObjectConfigurationAvailableException;
use C201\MediaBundle\Model\Exception\NoObjectFieldConfigurationAvailableException;
use C201\MediaBundle\Model\Media;
use C201\MediaBundle\Model\Object\ObjectField;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MediaSerializationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected readonly Media $media,
        private readonly MediaConfiguration $mediaConfiguration,
        private readonly ?CacheManager $cacheManager = null,
    ) {}

    public static function getSubscribedEvents() : array
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'onPreSerialize',
            ],
        ];
    }


    /**
     * @throws NoObjectConfigurationAvailableException
     * @throws NoObjectFieldConfigurationAvailableException
     */
    public function onPreSerialize(PreSerializeEvent $event) : void
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

            if (null === $this->cacheManager || !$objectFieldConfiguration->getLiipImagineFilters()) {
                $propertyAccessor->setValue($object, $field, $normalizedPath);
            } elseif ($object instanceof AttachmentInterface) {
                if ($object->getIsImage()) {
                    $propertyAccessor->setValue($object, $field, $this->applyLiipImagineFilters($objectFieldConfiguration, $normalizedPath));
                } else {
                    $propertyAccessor->setValue($object, $field, $normalizedPath);
                }
            } else {
                $propertyAccessor->setValue($object, $field, $this->applyLiipImagineFilters($objectFieldConfiguration, $normalizedPath));
            }
        }
    }

    protected function applyLiipImagineFilters(ObjectFieldConfiguration $objectFieldConfiguration, string $normalizedPath) : array
    {
        $configs = [];

        foreach ($objectFieldConfiguration->getLiipImagineFilters() as $serializationKey) {
            $configs[$serializationKey] = $this->cacheManager->getBrowserPath($normalizedPath, $serializationKey);
        }

        return $configs;
    }
}
