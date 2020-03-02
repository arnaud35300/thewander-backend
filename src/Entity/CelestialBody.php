<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CelestialBodyRepository")
 */
class CelestialBody
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"celestial-body"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"celestial-bodies", "celestial-body", "celestial-body-creation", "celestial-body-update"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"celestial-bodies", "celestial-body"})
     */
    private $slug;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"celestial-bodies", "celestial-body", "celestial-body-creation", "celestial-body-update"})
     */
    private $xPosition;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"celestial-bodies", "celestial-body", "celestial-body-creation", "celestial-body-update"})
     */
    private $yPosition;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"celestial-bodies", "celestial-body", "celestial-body-creation", "celestial-body-update"})
     */
    private $picture;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"celestial-body"})
     */
    private $nbStars;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"celestial-body", "celestial-body-creation", "celestial-body-update"})
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Property", inversedBy="celestialBodies")
     * @Groups({"celestial-body", "celestial-body-creation", "celestial-body-update"})
     */
    private $properties;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="celestialBodies")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"celestial-body"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="celestialBody", orphanRemoval=true)
     * @Groups({"celestial-body"})
     */
    private $comments;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"celestial-body"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"celestial-body"})
     */
    private $updatedAt;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getXPosition(): ?int
    {
        return $this->xPosition;
    }

    public function setXPosition(?int $xPosition): self
    {
        $this->xPosition = $xPosition;

        return $this;
    }

    public function getYPosition(): ?int
    {
        return $this->yPosition;
    }

    public function setYPosition(?int $yPosition): self
    {
        $this->yPosition = $yPosition;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getNbStars(): ?int
    {
        return $this->nbStars;
    }

    public function setNbStars(?int $nbStars): self
    {
        $this->nbStars = $nbStars;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Property[]
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(Property $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties[] = $property;
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->properties->contains($property)) {
            $this->properties->removeElement($property);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setCelestialBody($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getCelestialBody() === $this) {
                $comment->setCelestialBody(null);
            }
        }

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
}
