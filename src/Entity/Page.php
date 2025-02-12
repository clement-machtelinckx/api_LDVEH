<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PageRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['page:read']],
    denormalizationContext: ['groups' => ['page:write']]
)]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['page:read'])]
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

    public function __toString(): string
    {
        return $this->id; // Vous pouvez ajuster cela pour retourner une chaîne de caractères appropriée
    }

}
