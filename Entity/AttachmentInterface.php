<?php

namespace C201\MediaBundle\Entity;

interface AttachmentInterface
{
    public function getFileName();
    public function setFileName($fileName);
    public function getOriginalName();
    public function setOriginalName($originalName);
    public function getMimeType();
    public function setMimeType($mimeType);
    public function getIsImage();
    public function setIsImage($isImage);
    public function getSize();
    public function setSize($size);
}
