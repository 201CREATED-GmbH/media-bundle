<?php declare(strict_types=1);

namespace C201\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class File
{
    public const VERSION_HEAD = 'head';

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'integer')]
    protected ?int $version = null;

    #[ORM\Column(type: 'string')]
    protected ?string $name = null;

    #[ORM\Column(type: 'string')]
    protected ?string $path = null;

    #[ORM\Column(type: 'string')]
    protected ?string $mime = null;

    #[ORM\Column(type: 'string')]
    protected ?string $md5 = null;

    #[ORM\Column(type: 'integer')]
    protected ?int $size = null;

    #[ORM\ManyToOne(targetEntity: Media::class, inversedBy: 'files')]
    protected ?Media $media = null;

    #[ORM\Column(type: 'date')]
    protected ?\DateTime $uploadedAt = null;


    public function getId () : ?int
    {
        return $this->id;
    }


    public function getVersion () : ?int
    {
        return $this->version;
    }


    public function setVersion (int $version) : static
    {
        $this->version = $version;
        return $this;
    }


    public function getName () : ?string
    {
        return $this->name;
    }


    public function setName (string $name) : static
    {
        $this->name = $name;
        return $this;
    }


    public function getPath () : ?string
    {
        return $this->path;
    }


    public function setPath (string $path) : static
    {
        $this->path = $path;
        return $this;
    }


    public function getMime () : ?string
    {
        return $this->mime;
    }


    public function setMime (string $mime) : static
    {
        $this->mime = $mime;
        return $this;
    }


    public function getMd5 () : ?string
    {
        return $this->md5;
    }


    public function setMd5 (string $md5) : static
    {
        $this->md5 = $md5;
        return $this;
    }


    public function getSize () : ?int
    {
        return $this->size;
    }


    public function setSize (int $size) : static
    {
        $this->size = $size;
        return $this;
    }


    public function getMedia () : ?Media
    {
        return $this->media;
    }


    public function setMedia (?Media $media) : static
    {
        $this->media = $media;
        return $this;
    }


    public function getUploadedAt () : ?\DateTime
    {
        return $this->uploadedAt;
    }


    public function setUploadedAt (\DateTime $uploadedAt) : static
    {
        $this->uploadedAt = $uploadedAt;
        return $this;
    }
}
