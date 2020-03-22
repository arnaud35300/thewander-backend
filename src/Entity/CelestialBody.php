<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CelestialBodyRepository")
 * 
 * @ORM\Table(
 *      indexes={
 *          @ORM\Index(name="idx_name", columns={"name"}),
 *          @ORM\Index(name="idx_slug", columns={"slug"})
 *      }
 * )
 * 
 * @UniqueEntity("name")
 * @UniqueEntity("slug")
 * @UniqueEntity(
 *      fields={"xPosition", "yPosition"},
 *      message="Another celestial body already matches these coordinates."
 * )
 */
class CelestialBody
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups({"celestial-bodies", "celestial-body", "users", "comments"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="celestialBodies")
     * 
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"celestial-bodies", "celestial-body"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Icon")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"celestial-bodies"})
     */
    private $icon;

    /**
     * @ORM\Column(name="name", type="string", length=50)
     * 
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *      max=50
     * )
     * 
     * @Groups({"celestial-bodies", "celestial-body", "celestial-body-creation", "users", "user", "current-user", "comments"})
     */
    private $name;

    /**
     * @ORM\Column(name="slug", type="string", length=50)
     * 
     * @Groups({"celestial-bodies", "celestial-body", "users", "user", "current-user", "comments"})
     */
    private $slug;

    /**
     * @ORM\Column(name="xPosition", type="integer")
     *
     * @Assert\Type(type="integer")
     * @Assert\Length(
     *      max=6
     * )
     * 
     * @Groups({"celestial-bodies", "celestial-body", "celestial-body-creation", "user", "current-user"})
     */
    private $xPosition;

    /**
     * @ORM\Column(name="yPosition", type="integer")
     *
     * @Assert\Type(type="integer")
     * @Assert\Length(
     *      max=6
     * )
     * 
     * @Groups({"celestial-bodies", "celestial-body", "celestial-body-creation", "user", "current-user"})
     */
    private $yPosition;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * 
     * @Assert\Type("string")
     * @Assert\Length(
     *      max=50
     * )
     * 
     * @Groups({"celestial-body", "user", "current-user"})
     */
    private $picture;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Assert\Type("string")
     * @Assert\Length(
     *      max=2000
     * )
     * 
     * @Groups({"celestial-body", "celestial-body-creation"})
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Property", inversedBy="celestialBodies")
     * 
     * @Groups({"celestial-body", "user", "current-user"})
     */
    private $properties;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="celestialBody", orphanRemoval=true)
     * 
     * @Groups({"celestial-body", "current-user"})
     */
    private $comments;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Assert\NotBlank
     * 
     * @Groups({"celestial-bodies", "celestial-body", "user", "current-user"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Assert\NotBlank
     * 
     * @Groups({"celestial-bodies", "celestial-body", "user", "current-user"})
     */
    private $updatedAt;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIcon(): ?Icon
    {
        return $this->icon;
    }

    public function setIcon(?Icon $icon): self
    {
        $this->icon = $icon;

        return $this;
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