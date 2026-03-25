<?php

namespace App\Entity;

use App\Enum\EquipmentSlot;
use App\Enum\EquipmentType;
use App\Repository\EquipmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
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

    #[ORM\Column(length: 50, enumType: EquipmentType::class)]
    private ?EquipmentType $type = null;

    #[ORM\Column(length: 20, nullable: true, enumType: EquipmentSlot::class)]
    private ?EquipmentSlot $slot = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $enduranceBonus = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $healAmount = 0;

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

    public function getType(): ?EquipmentType
    {
        return $this->type;
    }

    public function setType(EquipmentType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSlot(): ?EquipmentSlot
    {
        return $this->slot;
    }

    public function setSlot(?EquipmentSlot $slot): static
    {
        $this->slot = $slot;

        return $this;
    }

    public function getTypeLabel(): string
    {
        return $this->type?->label() ?? '';
    }

    public function getSlotLabel(): string
    {
        return $this->slot?->label() ?? '-';
    }

    public function isConsumable(): bool
    {
        return $this->type?->isConsumable() ?? false;
    }

    public function goesInBackpack(): bool
    {
        return $this->type?->goesInBackpack() ?? false;
    }

    public function getEnduranceBonus(): int
    {
        return $this->enduranceBonus;
    }

    public function setEnduranceBonus(int $enduranceBonus): static
    {
        $this->enduranceBonus = $enduranceBonus;

        return $this;
    }

    public function getHealAmount(): int
    {
        return $this->healAmount;
    }

    public function setHealAmount(int $healAmount): static
    {
        $this->healAmount = $healAmount;

        return $this;
    }

}
