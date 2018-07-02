<?php

namespace C201\MediaBundle\Event;

final class MediaEvents
{
    /**
     * The c201_media.download event is thrown each time a download is triggered
     *
     * The event listener receivers an C201\MediaBundle\Event\MediaEvent instance
     */
    const DOWNLOAD = 'c201_media.download';
}
