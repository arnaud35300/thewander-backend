<?php

namespace App\Entity;

use App\Service\Slugger;
use App\Service\UserAttributes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

 /**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(
 *      indexes={
 *          @ORM\Index(name="idx_nickname", columns={"nickname"}),
 *          @ORM\Index(name="idx_slug", columns={"slug"})
 *      }
 * )
 * 
 * @UniqueEntity("nickname")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups({"celestial-bodies", "celestial-body", "users", "user", "comments", "current-user"})
     */
    private $id;

    /**
     * @ORM\Column(name="nickname", type="string", length=30, unique=true)
     * 
     * @Assert\Length(
     *      min = 3,
     *      max = 30
     * )
     * @Assert\NotBlank
     * @Assert\Type("string")
     * 
     * @Groups({"celestial-bodies", "celestial-body", "user-creation", "user-update", "users", "user", "current-user", "comments"})
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=30)
     * 
     * @Groups({"celestial-bodies", "celestial-body", "users", "user-update", "user", "current-user", "comments"})
     */
    private $slug;

    /**
     * @ORM\Column(name="email", type="string", length=180, unique=true)
     * 
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Email
     * @Assert\Length(
     *      max = 100,
     * )
     * 
     * @Groups({"users", "user-creation", "user-update", "current-user"})
     */
    private $email;

    /**
     * @var string The hashed password
     * 
     * @ORM\Column(type="string")
     * 
     * @Assert\Length(
     *      max = 150
     * )
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Regex(
     *      pattern = "#.*^(?=.{8,150})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#",
     *      match=true,
     *      message="Your password must be at least eight characters long, including upper and lower case letters, a number and a symbol."
     * )
     * 
     * @Groups({"user-creation"}) 
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"celestial-bodies", "celestial-body", "users", "current-user", "user"})
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * 
     * @Assert\Length(
     *      max = 50
     * )
     * @Assert\Type("string")
     * 
     * @Groups({"celestial-body", "user-update", "users", "user", "current-user", "comments"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * 
     * @Assert\Length(
     *      max = 50
     * )
     * @Assert\Type("string")
     * 
     * @Groups({"user-update", "user", "current-user"})
     * */
    private $firstname;

    /**
     * @ORM\Column(type="date", nullable=true)
     * 
     * @Groups({"user-update", "user", "current-user"})
     */
    private $birthday;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Assert\Length(
     *      max = 500
     * )
     * @Assert\Type("string")
     * 
     * @Groups({"user-update", "user", "current-user"})
     */
    private $bio;

    /**
     * @ORM\Column(type="smallint")
     * 
     * @Assert\Type("integer")
     * 
     * @Groups({"celestial-bodies", "users", "user", "current-user"})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CelestialBody", mappedBy="user", orphanRemoval=true)
     * 
     * @Groups({"users", "user", "current-user"})
     */
    private $celestialBodies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     * 
     * @Groups({"users", "user", "current-user"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rank", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"celestial-body", "user", "current-user"})
     */
    private $rank;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups({"user", "current-user"})
     */
    private $experience;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * 
     * @Groups({"user-update", "users", "user", "current-user"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * 
     * @Groups({"user-update", "users", "current-user"})
     */
    private $updatedAt;

    public function __construct()
    {
        $this->celestialBodies = new ArrayCollection();
        $this->comments = new ArrayCollection();

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        $this->status = (int) 1;
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

    public function setFirstname(?string $firstname): self
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
    
    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(?int $experience): self
    {
        $this->experience = $experience;

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
