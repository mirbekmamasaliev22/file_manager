<?php

namespace App\Entity;

use App\Interfaces\IEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Document implements IEntity
{
    const TARGET_DIR = "uploads/images/";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Preview", mappedBy="document", orphanRemoval=true)
     */
    private $previews;

    public function __construct()
    {
        $this->previews = new ArrayCollection();
    }

    /** @ORM\PrePersist */
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /** @ORM\PreUpdate */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }

    /**
     * @return Collection|Preview[]
     */
    public function getPreviews(): Collection
    {
        return $this->previews;
    }

    public function addPreview(Preview $preview): self
    {
        if (!$this->previews->contains($preview)) {
            $this->previews[] = $preview;
            $preview->setDocument($this);
        }

        return $this;
    }

    public function removePreview(Preview $preview): self
    {
        if ($this->previews->contains($preview)) {
            $this->previews->removeElement($preview);
            // set the owning side to null (unless already changed)
            if ($preview->getDocument() === $this) {
                $preview->setDocument(null);
            }
        }

        return $this;
    }
}
