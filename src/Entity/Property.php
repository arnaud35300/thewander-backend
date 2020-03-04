<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
 * @UniqueEntity("name")
 */
class Property
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"celestial-body"})
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Groups({"celestial-body", "user-celestial-body"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer")
     * @Groups({"celestial-body", "user-celestial-body"})
     */
    private $unit;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Type("string")
     * @Groups({"celestial-body", "user-celestial-body"})
     */
    private $value;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CelestialBody", mappedBy="properties")
     */
    private $celestialBodies;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\DateTime
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

    public function getUnit(): ?int
    {
        return $this->unit;
    }

    public function setUnit(?int $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

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
