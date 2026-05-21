<?php declare(strict_types=1);

namespace C201\MediaBundle\Entity;

interface AttachmentInterface
{
    public function getFileName () : ?string;


    public function setFileName (?string $fileName) : static;


    public function getOriginalName () : ?string;


    public function setOriginalName (?string $originalName) : static;


    public function getMimeType () : ?string;


    public function setMimeType (?string $mimeType) : static;


    public function getIsImage () : ?bool;


    public function setIsImage (?bool $isImage) : static;


    public function getSize () : ?int;


    public function setSize (?int $size) : static;
}
