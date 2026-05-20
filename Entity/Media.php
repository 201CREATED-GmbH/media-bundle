<?php

namespace C201\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \C201\MediaBundle\Repository\MediaRepository::class)]
#[ORM\Table]
#[ORM\Index(name: 'owner', columns: ['owner'])]
class Media
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'integer')]
    protected $versions = 0;

    #[ORM\Column(type: 'string')]
    protected $name;

    #[ORM\OneToMany(targetEntity: \C201\MediaBundle\Entity\File::class, mappedBy: 'media')]
    protected $files;

    #[ORM\Column(type: 'string')]
    protected $context;

    #[ORM\Column(type: 'string')]
    protected $owner;

    #[ORM\Column(type: 'string')]
    protected $type;

    #[ORM\Column(type: 'date')]
    protected $createdAt;

    #[ORM\Column(type: 'date', nullable: true)]
    protected $deletedAt;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getLatestFile()
    {
        if (!count($this->files)) {
            return null;
        }

        return $this->files[count($this->files) - 1];
    }

    public function getFileVersion($version = File::VERSION_HEAD)
    {
        if (File::VERSION_HEAD === $version) {
            return $this->getLatestFile();
        }

        foreach ($this->files as $file) {
            if ($version == $file->getVersion()) {
                return $file;
            }
        }

        throw new \LogicException(sprintf('No file with Version "%d" in "%s"', $version, $this->getName()));
    }

    public function delete()
    {
        if (null !== $this->deletedAt) {
            throw new \LogicException('Already deleted item cannot be deleted.');
        }
        $this->deletedAt = new \DateTime('now');
    }

    public function undelete()
    {
        if (null === $this->deletedAt) {
            throw new \LogicException('Not deleted item cannot be undeleted.');
        }
        $this->deletedAt = null;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; return $this; }
    public function getVersions() { return $this->versions; }
    public function setVersions($versions) { $this->versions = $versions; return $this; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; return $this; }
    public function getFiles() { return $this->files; }
    public function addFile(File $file) { $this->files[] = $file; return $this; }
    public function removeFile(File $file) { $this->files[] = $file; return $this; }
    public function getContext() { return $this->context; }
    public function setContext($context) { $this->context = $context; return $this; }
    public function getOwner() { return $this->owner; }
    public function setOwner($owner) { $this->owner = $owner; return $this; }
    public function getType() { return $this->type; }
    public function setType($type) { $this->type = $type; return $this; }
    public function getCreatedAt() { return $this->createdAt; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; return $this; }
    public function getDeletedAt() { return $this->deletedAt; }
    public function setDeletedAt($deletedAt) { $this->deletedAt = $deletedAt; return $this; }
}
