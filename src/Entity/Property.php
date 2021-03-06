<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
 * 
 * @ORM\Table(
 *      indexes={
 *          @ORM\Index(name="idx_name", columns={"name"})
 *      }
 * )
 * 
 * @UniqueEntity("name")
 */
class Property
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups({"properties", "celestial-body"})
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     * 
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *      max=100
     * )
     * 
     * @Groups({"properties", "user", "current-user", "celestial-body"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CelestialBody", mappedBy="properties")
     */
    private $celestialBodies;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->celestialBodies = new ArrayCollection();
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

    /**
     * @return Collection|CelestialBody[]
     */
    public function getCelestialBodies(): Collection
    {
        return $this->celestialBodies;
    }

    public function addCelestialBody(CelestialBody $celestialBody): self
    {
        if (!$this->celestialBodies->contains($celestialBody)) {
            $this->celestialBodies[] = $celestialBody;
            $celestialBody->addProperty($this);
        }

        return $this;
    }

    public function removeCelestialBody(CelestialBody $celestialBody): self
    {
        if ($this->celestialBodies->contains($celestialBody)) {
            $this->celestialBodies->removeElement($celestialBody);
            $celestialBody->removeProperty($this);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }
}