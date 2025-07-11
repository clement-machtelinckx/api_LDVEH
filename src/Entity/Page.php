<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PageRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: "page", uniqueConstraints: [
    new ORM\UniqueConstraint(name: "unique_page_number_per_book", columns: ["book_id", "page_number"])
])]
#[ApiResource(
    normalizationContext: ['groups' => ['page:read']],
    denormalizationContext: ['groups' => ['page:write']],
    security: "is_granted('ROLE_ADMIN')"
)]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['page:read', 'adventurer:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['page:read', 'page:write'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'page')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['page:read', 'page:write'])]
    private ?Book $book = null;

    /**
     * @var Collection<int, Choice>
     */
    #[ORM\OneToMany(targetEntity: Choice::class, mappedBy: 'page', orphanRemoval: true, cascade: ['persist'], fetch: 'LAZY')]
    #[Groups(['page:read', 'page:write'])]
    #[MaxDepth(1)]
    private Collection $choices;

    #[ORM\Column]
    #[Groups(['page:read', 'page:write', 'adventurer:read'])]
    private ?int $pageNumber = null;

    #[ORM\ManyToOne]
    private ?Monster $monster = null;

    #[ORM\Column(nullable: true)]
    private ?bool $combatIsBlocking = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['page:read', 'adventurer:read'])]
    private ?string $endingType = null; // "death", "victory", or null


    public function __construct()
    {
        $this->choices = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    /**
     * @return Collection<int, Choice>
     */
    public function getChoices(): Collection
    {
        return $this->choices;
    }

    public function addChoice(Choice $choice): static
    {
        if (!$this->choices->contains($choice)) {
            $this->choices->add($choice);
            $choice->setPage($this);
        }

        return $this;
    }

    public function removeChoice(Choice $choice): static
    {
        if ($this->choices->removeElement($choice)) {
            // set the owning side to null (unless already changed)
            if ($choice->getPage() === $this) {
                $choice->setPage(null);
            }
        }

        return $this;
    }

    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): static
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }
    
    public function __toString(): string
    {
        return $this->id; // Vous pouvez ajuster cela pour retourner une chaîne de caractères appropriée
    }

    public function getMonster(): ?Monster
    {
        return $this->monster;
    }

    public function setMonster(?Monster $monster): static
    {
        $this->monster = $monster;

        return $this;
    }

    public function isCombatIsBlocking(): ?bool
    {
        return $this->combatIsBlocking;
    }

    public function setCombatIsBlocking(?bool $combatIsBlocking): static
    {
        $this->combatIsBlocking = $combatIsBlocking;

        return $this;
    }

    public function getEndingType(): ?string
    {
        return $this->endingType;
    }

    public function setEndingType(?string $endingType): static
    {
        $this->endingType = $endingType;

        return $this;
    }

    public function isEnding(): bool
    {
        return $this->endingType !== null;
    }

    public function isVictory(): bool
    {
        return $this->endingType === 'victory';
    }

}
