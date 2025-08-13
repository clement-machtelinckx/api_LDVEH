<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\FeedbackRepository;
use ApiPlatform\Metadata\GetCollection;
use App\State\Feedback\FeedbackCreateProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/feedback',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            denormalizationContext: ['groups' => ['feedback:write']],
            normalizationContext: ['groups' => ['feedback:read']],
            processor: FeedbackCreateProcessor::class
        ),
        new GetCollection(
            uriTemplate: '/feedback',
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['feedback:read']]
        ),
        new Get(
            uriTemplate: '/feedback/{id}',
            security: "is_granted('ROLE_ADMIN') or object.getUser() == user",
            normalizationContext: ['groups' => ['feedback:read']]
        ),
    ],
)]
#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['feedback:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email]
    #[Groups(['feedback:read'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 5000)]
    #[Groups(['feedback:write', 'feedback:read'])]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['feedback:write', 'feedback:read'])]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $rating = null;

    #[ORM\Column]
    #[Groups(['feedback:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50)]
    #[Groups(['feedback:read'])]
    private ?string $status = 'new';

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
