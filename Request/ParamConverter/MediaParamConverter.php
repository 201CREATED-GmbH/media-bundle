<?php

namespace C201\MediaBundle\Request\ParamConverter;

use C201\MediaBundle\Manager\MediaManager;
use C201\MediaBundle\Entity\Media;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaParamConverter implements ParamConverterInterface
{
    protected $manager;


    /**
     * @param MediaManager $manager
     */
    public function __construct(MediaManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $id = $request->attributes->get('mediaId', $request->attributes->get('id'));

        // everything okay when parameter is optional
        if (!$id && $configuration->isOptional()) {
            return true;
        }

        $media = $this->manager->findOneById($id);
        if (!$media) {
            throw new NotFoundHttpException(sprintf('Requested Media not found (%s).', $id));
        }

        $request->attributes->set($configuration->getName(), $media);

        return true;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return Media::class === $configuration->getClass();
    }
}
