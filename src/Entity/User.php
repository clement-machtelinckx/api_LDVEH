<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    security: "is_granted('ROLE_ADMIN')"
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:write'])]
    private ?string $password = null;

    /**
     * @var Collection<int, Adventurer>
     */
    #[ORM\OneToMany(targetEntity: Adventurer::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $adventurers;

    /**
     * @var Collection<int, Adventure>
     */
    #[ORM\OneToMany(targetEntity: Adventure::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $adventures;

    /**
     * @var Collection<int, AdventureHistory>
     */
    #[ORM\OneToMany(targetEntity: AdventureHistory::class, mappedBy: 'user')]
    private Collection $adventureHistories;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $nickname = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $gender = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?\DateTimeInterface $dateOfBirth = null;

    public function __construct()
    {
        $this->adventurers = new ArrayCollection();
        $this->adventures = new ArrayCollection();
        $this->adventureHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Adventurer>
     */
    public function getAdventurers(): Collection
    {
        return $this->adventurers;
    }

    public function addAdventurer(Adventurer $adventurer): static
    {
        if (!$this->adventurers->contains($adventurer)) {
            $this->adventurers->add($adventurer);
            $adventurer->setUser($this);
        }

        return $this;
    }

    public function removeAdventurer(Adventurer $adventurer): static
    {
        if ($this->adventurers->removeElement($adventurer)) {
            // set the owning side to null (unless already changed)
            if ($adventurer->getUser() === $this) {
                $adventurer->setUser(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Adventure>
     */
    public function getAdventures(): Collection
    {
        return $this->adventures;
    }

    public function addAdventure(Adventure $adventure): static
    {
        if (!$this->adventures->contains($adventure)) {
            $this->adventures->add($adventure);
            $adventure->setUser($this);
        }

        return $this;
    }

    public function removeAdventure(Adventure $adventure): static
    {
        if ($this->adventures->removeElement($adventure)) {
            // set the owning side to null (unless already changed)
            if ($adventure->getUser() === $this) {
                $adventure->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AdventureHistory>
     */
    public function getAdventureHistories(): Collection
    {
        return $this->adventureHistories;
    }

    public function addAdventureHistory(AdventureHistory $adventureHistory): static
    {
        if (!$this->adventureHistories->contains($adventureHistory)) {
            $this->adventureHistories->add($adventureHistory);
            $adventureHistory->setUser($this);
        }

        return $this;
    }

    public function removeAdventureHistory(AdventureHistory $adventureHistory): static
    {
        if ($this->adventureHistories->removeElement($adventureHistory)) {
            // set the owning side to null (unless already changed)
            if ($adventureHistory->getUser() === $this) {
                $adventureHistory->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }
}
