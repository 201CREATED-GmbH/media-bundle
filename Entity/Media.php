<?php declare(strict_types=1);

namespace C201\MediaBundle\Entity;

use C201\MediaBundle\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\Table]
#[ORM\Index(name: 'owner', columns: ['owner'])]
class Media
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'integer')]
    protected int $versions = 0;

    #[ORM\Column(type: 'string')]
    protected ?string $name = null;

    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'media')]
    protected Collection $files;

    #[ORM\Column(type: 'string')]
    protected ?string $context = null;

    #[ORM\Column(type: 'string')]
    protected ?string $owner = null;

    #[ORM\Column(type: 'string')]
    protected ?string $type = null;

    #[ORM\Column(type: 'date')]
    protected ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'date', nullable: true)]
    protected ?\DateTime $deletedAt = null;


    public function __construct ()
    {
        $this->files = new ArrayCollection();
    }


    public function __toString () : string
    {
        return (string) $this->name;
    }


    public function getLatestFile () : ?File
    {
        if ($this->files->isEmpty()) {
            return null;
        }

        return $this->files->last() ?: null;
    }


    public function getFileVersion (mixed $version = File::VERSION_HEAD) : ?File
    {
        if (File::VERSION_HEAD === $version) {
            return $this->getLatestFile();
        }

        foreach ($this->files as $file) {
            if ($version === $file->getVersion()) {
                return $file;
            }
        }

        throw new \LogicException(\sprintf('No file with Version "%d" in "%s"', $version, $this->getName()));
    }


    public function delete () : void
    {
        if (null !== $this->deletedAt) {
            throw new \LogicException('Already deleted item cannot be deleted.');
        }
        $this->deletedAt = new \DateTime('now');
    }


    public function undelete () : void
    {
        if (null === $this->deletedAt) {
            throw new \LogicException('Not deleted item cannot be undeleted.');
        }
        $this->deletedAt = null;
    }


    public function getId () : ?int
    {
        return $this->id;
    }


    public function setId (int $id) : static
    {
        $this->id = $id;
        return $this;
    }


    public function getVersions () : int
    {
        return $this->versions;
    }


    public function setVersions (int $versions) : static
    {
        $this->versions = $versions;
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


    public function getFiles () : Collection
    {
        return $this->files;
    }


    public function addFile (File $file) : static
    {
        $this->files[] = $file;
        return $this;
    }


    public function removeFile (File $file) : static
    {
        $this->files->removeElement($file);
        return $this;
    }


    public function getContext () : ?string
    {
        return $this->context;
    }


    public function setContext (string $context) : static
    {
        $this->context = $context;
        return $this;
    }


    public function getOwner () : ?string
    {
        return $this->owner;
    }


    public function setOwner (string $owner) : static
    {
        $this->owner = $owner;
        return $this;
    }


    public function getType () : ?string
    {
        return $this->type;
    }


    public function setType (string $type) : static
    {
        $this->type = $type;
        return $this;
    }


    public function getCreatedAt () : ?\DateTime
    {
        return $this->createdAt;
    }


    public function setCreatedAt (\DateTime $createdAt) : static
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    public function getDeletedAt () : ?\DateTime
    {
        return $this->deletedAt;
    }


    public function setDeletedAt (?\DateTime $deletedAt) : static
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}
