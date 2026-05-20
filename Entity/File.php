<?php

namespace C201\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class File
{
    const VERSION_HEAD = 'head';

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'integer')]
    protected $version;

    #[ORM\Column(type: 'string')]
    protected $name;

    #[ORM\Column(type: 'string')]
    protected $path;

    #[ORM\Column(type: 'string')]
    protected $mime;

    #[ORM\Column(type: 'string')]
    protected $md5;

    #[ORM\Column(type: 'integer')]
    protected $size;

    #[ORM\ManyToOne(targetEntity: \C201\MediaBundle\Entity\Media::class, inversedBy: 'files')]
    protected $media;

    #[ORM\Column(type: 'date')]
    protected $uploadedAt;

    public function getId() { return $this->id; }
    public function getVersion() { return $this->version; }
    public function setVersion($version) { $this->version = $version; return $this; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; return $this; }
    public function getPath() { return $this->path; }
    public function setPath($path) { $this->path = $path; return $this; }
    public function getMime() { return $this->mime; }
    public function setMime($mime) { $this->mime = $mime; return $this; }
    public function getMd5() { return $this->md5; }
    public function setMd5($md5) { $this->md5 = $md5; return $this; }
    public function getSize() { return $this->size; }
    public function setSize($size) { $this->size = $size; return $this; }
    public function getUploadedAt() { return $this->uploadedAt; }
    public function setUploadedAt($uploadedAt) { $this->uploadedAt = $uploadedAt; return $this; }
}
