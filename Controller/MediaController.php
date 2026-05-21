<?php declare(strict_types=1);

namespace C201\MediaBundle\Controller;

use C201\MediaBundle\Entity\Media;
use C201\MediaBundle\Event\MediaEvent;
use C201\MediaBundle\Event\MediaEvents;
use C201\MediaBundle\Manager\DownloadHistoryManager;
use C201\MediaBundle\Model\MediaLibrary;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/media')]
class MediaController extends AbstractController
{
    public function __construct(
        private readonly MediaLibrary $mediaLibrary,
        private readonly DownloadHistoryManager $downloadHistoryManager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    #[Route('/download/{mediaId}/{version}/', name: 'c201_media_download', defaults: ['version' => 'head'])]
    public function downloadAction(Request $request, Media $media, string $version = 'head') : BinaryFileResponse
    {
        $path = $this->mediaLibrary->getMediaFilePath($media, $version, true);
        $file = $media->getFileVersion($version);

        $this->dispatchDownloadEvent($media, $version);
        $this->trackDownload($request, $media);

        $downloadFile = new \SplFileInfo($path);

        $response = new BinaryFileResponse($downloadFile);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            \sprintf('%s.%s', \preg_replace('@\\.[a-z0-9]+$@i', '', $media->getName()), $downloadFile->getExtension()),
            \iconv('UTF-8', 'ASCII//TRANSLIT', $file->getName() ?? '') ?: ''
        );

        return $response;
    }

    private function trackDownload(Request $request, Media $media) : void
    {
        $user = $this->getUser();
        $this->downloadHistoryManager->trackDownload(
            $media,
            $request->getClientIp(),
            $request->headers->get('referer'),
            $request->headers->get('User-Agent'),
            $user?->getUserIdentifier(),
        );
    }

    private function dispatchDownloadEvent(Media $media, string $version) : void
    {
        $this->eventDispatcher->dispatch(
            new MediaEvent($media, $version, $this->getUser()),
            MediaEvents::DOWNLOAD,
        );
    }
}
