<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IconRepository")
 * 
 * @UniqueEntity("pattern")
 * @UniqueEntity("path")
 */
class Icon
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups({"icons", "celestial-bodies"})
     */
    private $id;

    /**
     * @ORM\Column(name="pattern", type="string", length=50)
     * 
     * @Groups({"icons", "celestial-bodies"})
     */
    private $pattern;

    /**
     * @ORM\Column(name="path", type="string", length=100)
     * 
     * @Groups({"icons", "celestial-bodies"})
     */
    private $path;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

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