<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ChoiceRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: ChoiceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['choice:read']],
    denormalizationContext: ['groups' => ['choice:write']]
)]
class Choice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['choice:read', 'page:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['choice:read', 'choice:write', 'page:read', 'page:write'])]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'choices')]
    #[Groups(['choice:read', 'choice:write'])]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    private ?Page $page = null;

    #[ORM\ManyToOne(targetEntity: Page::class)]
    #[ORM\JoinColumn(nullable: true)] // a mettre false dans le future 
    #[Groups(['choice:read', 'choice:write', 'page:read', 'page:write'])]
    #[MaxDepth(1)]
    private ?Page $nextPage = null;

    #[Groups(['choice:read', 'choice:write'])]
    private ?int $nextPageNumber = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getNextPage(): ?Page
    {
        return $this->nextPage;
    }

    public function setNextPage(?Page $nextPage): static
    {
        $this->nextPage = $nextPage;

        return $this;
    }

    public function setNextPageNumber(?int $pageNumber): static
    {
        if ($pageNumber !== null) {
            // On ne crée pas la page ici ! Juste stocker temporairement le numéro
            $this->nextPageNumber = $pageNumber;
        }
        return $this;
    }
    
    public function getNextPageNumber(): ?int
    {
        return $this->nextPage ? $this->nextPage->getPageNumber() : $this->nextPageNumber;
    }
    


    public function __toString(): string
    {
        return $this->id; // Vous pouvez ajuster cela pour retourner une chaîne de caractères appropriée
    }
}
