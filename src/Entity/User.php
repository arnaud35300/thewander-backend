<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"celestial-body", "users_list", "user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Groups({"celestial-body", "user_create", "user_updated", "users_list", "user"})
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage="Your nickname must be at least {{ limit }} characters long",
     *      maxMessage = "Your nickname cannot be longer than {{ limit }} characters"
     * )
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups({"celestial-body", "users_list", "user_updated", "user"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user_create", "user_updated"})
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user_create"})
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"celestial-body", "user_updated", "users_list", "user"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"user_updated", "user"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"user_updated", "user"})
     */
    private $birthday;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user_updated", "user"})
     */
    private $bio;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"user"})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CelestialBody", mappedBy="user", orphanRemoval=true)
     * @Groups("user_celestial_body")
     */
    private $celestialBodies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     * @Groups({"user"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rank", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"celestial-body", "user"})
     */
    private $rank;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user_updated", "user"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user_updated", "user"})
     */
    private $updatedAt;

    public function __construct()
    {
        $this->celestialBodies = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * ? The mandatory abstract method from UserInterface.
     * ! @see $this->getRole()
     */
    public function getRoles(): array
    {
        return [$this->getRole()->getName()];
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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
            $celestialBody->setUser($this);
        }

        return $this;
    }

    public function removeCelestialBody(CelestialBody $celestialBody): self
    {
        if ($this->celestialBodies->contains($celestialBody)) {
            $this->celestialBodies->removeElement($celestialBody);
            // set the owning side to null (unless already changed)
            if ($celestialBody->getUser() === $this) {
                $celestialBody->setUser(null);
            }
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getRank(): ?Rank
    {
        return $this->rank;
    }

    public function setRank(?Rank $rank): self
    {
        $this->rank = $rank;

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