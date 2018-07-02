<?php

namespace C201\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="C201\MediaBundle\Repository\MediaRepository")
 * @ORM\Table(indexes={@ORM\Index(name="owner", columns={"owner"})})
 */
class Media
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $versions = 0;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="C201\MediaBundle\Entity\File", mappedBy="media")
     */
    protected $files;

    /**
     * @ORM\Column(type="string")
     */
    protected $context;

    /**
     * @ORM\Column(type="string")
     */
    protected $owner;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="date")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="date")
     */
    protected $deletedAt;

    /**
     *
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }


    /**
     * @return File
     */
    public function getLatestFile()
    {
        if (!count($this->files)) {
            return null;
        }

        return $this->files[count($this->files) - 1];
    }

    /**
     * Finds the correct version of the file
     *
     * @param string $version
     *
     * @return File
     */
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

    /**
     * delete
     *
     * @throws \LogicException  when deletion fails
     */
    public function delete()
    {
        if (null !== $this->deletedAt) {
            throw new \LogicException('Already deleted item cannot be deleted.');
        }

        $this->deletedAt = new \DateTime('now');
    }

    /**
     * undelete
     *
     * @throws \LogicException  when undeletion fails
     */
    public function undelete()
    {
        if (null === $this->deletedAt) {
            throw new \LogicException('Not deleted item cannot be undeleted.');
        }

        $this->deletedAt = null;
    }

    /**
     * Gets id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets id
     *
     * @param mixed $id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets versions
     *
     * @return mixed
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * Sets versions
     *
     * @param mixed $versions
     *
     * @return static
     */
    public function setVersions($versions)
    {
        $this->versions = $versions;

        return $this;
    }

    /**
     * Gets name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets name
     *
     * @param mixed $name
     *
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets files
     *
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Sets files
     *
     * @param File $file
     *
     * @return static
     */
    public function addFile(File $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Sets files
     *
     * @param File $file
     *
     * @return static
     */
    public function removeFile(File $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Gets context
     *
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets context
     *
     * @param mixed $context
     *
     * @return static
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Gets owner
     *
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets owner
     *
     * @param mixed $owner
     *
     * @return static
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Gets type
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets type
     *
     * @param mixed $type
     *
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets createdAt
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets createdAt
     *
     * @param mixed $createdAt
     *
     * @return static
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Gets deletedAt
     *
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Sets deletedAt
     *
     * @param mixed $deletedAt
     *
     * @return static
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
