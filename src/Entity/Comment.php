<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks() 
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups({"celestial-body", "user", "comments"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank
     * @Assert\Type("string")
     * 
     * @Groups({"celestial-body", "user", "comments", "comment-creation", "comment-update"})
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"celestial-body", "comments"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CelestialBody", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"comments"})
     */
    private $celestialBody;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Assert\NotBlank
     * 
     * @Groups({"celestial-body", "user", "comments"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Assert\NotBlank
     * 
     * @Groups({"celestial-body", "user", "comments"})
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

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

    public function getCelestialBody(): ?CelestialBody
    {
        return $this->celestialBody;
    }

    public function setCelestialBody(?CelestialBody $celestialBody): self
    {
        $this->celestialBody = $celestialBody;

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
