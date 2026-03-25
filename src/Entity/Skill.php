<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Adventurer>
     */
    #[ORM\ManyToMany(targetEntity: Adventurer::class, mappedBy: 'skills')]
    private Collection $adventurers;

    public function __construct()
    {
        $this->adventurers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
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
            $adventurer->addSkill($this);
        }

        return $this;
    }

    public function removeAdventurer(Adventurer $adventurer): static
    {
        if ($this->adventurers->removeElement($adventurer)) {
            $adventurer->removeSkill($this);
        }

        return $this;
    }
}
