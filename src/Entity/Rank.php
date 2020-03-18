<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RankRepository")
 * 
 * @UniqueEntity("name")
 * @UniqueEntity("badge")
 * @UniqueEntity("rankNumber")
 */
class Rank
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(name="name", type="string", length=30, unique=true)
     * 
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 30
     * )
     * 
     * @Groups({"celestial-body", "user", "current-user"})
     */
    private $name;

    /**
     * @ORM\Column(name="badge", type="string", length=50, nullable=true)
     * 
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 50
     * )
     * 
     * @Groups({"celestial-body", "user", "current-user"})
     */
    private $badge;

    /**
     * @ORM\Column(name="rankNumber", type="smallint", unique=true)
     */
    private $rankNumber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="rank")
     */
    private $users;

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
        
        $this->users = new ArrayCollection();
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

    public function getBadge(): ?string
    {
        return $this->badge;
    }

    public function setBadge(?string $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function getRankNumber(): ?int
    {
        return $this->rankNumber;
    }

    public function setRankNumber(int $rankNumber): self
    {
        $this->rankNumber = $rankNumber;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRank($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getRank() === $this) {
                $user->setRank(null);
            }
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