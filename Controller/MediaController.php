<?php

namespace C201\MediaBundle\Controller;

use C201\MediaBundle\Entity\Media;
use C201\MediaBundle\Event\MediaEvent;
use C201\MediaBundle\Event\MediaEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * @Route("/download/{mediaId}/{version}/", name="c201_media_download", defaults={ "version": "head" })
     *
     * @param Media $media
     *
     * @return BinaryFileResponse
     */
    public function downloadAction(Request $request, Media $media, $version)
    {
        $path = $this
            ->get('c201_media.media_library')
            ->getMediaFilePath($media, $version, true);

        $file = $media->getFileVersion($media, $version);

        $this->dispatchUploadEvent($media, $version);
        $this->trackDownload($request, $media);

        $downloadFile = new \SplFileInfo($path);

        $response = new BinaryFileResponse($downloadFile);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.%s', preg_replace('@\.[a-z0-9]+$@i', '', $media->getName()), $downloadFile->getExtension()),
            iconv('UTF-8', 'ASCII//TRANSLIT', $file->getName())
        );

        return $response;
    }

    /**
     * @param Request $request
     * @param Media   $media
     */
    protected function trackDownload(Request $request, Media $media)
    {
        $this
            ->get('c201_media.manager.download_history')
            ->trackDownload(
                $media,
                $request->getClientIp(),
                $request->headers->get('referer'),
                $request->headers->get('User-Agent'),
                $this->getUser() ? $this->getUser()->getId() : null
            );
    }

    /**
     * @param Media $media
     * @param       $version
     */
    protected function dispatchUploadEvent(Media $media, $version)
    {
        // trigger media event
        $this->get('event_dispatcher')->dispatch(
            MediaEvents::DOWNLOAD,
            new MediaEvent($media, $version, $this->getUser())
        );
    }
}
