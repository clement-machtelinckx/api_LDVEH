<?php

namespace App\Service;

use App\Entity\Adventurer;
use App\Entity\Choice;
use App\Entity\Page;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;

class NavigationService
{
    public function __construct(
        private EquipmentService $equipmentService,
        private EquipmentRepository $equipmentRepo,
        private EntityManagerInterface $em,
    ) {}

    public function checkCondition(Adventurer $adventurer, array $condition): bool
    {
        $type = $condition['type'] ?? null;
        $slug = $condition['slug'] ?? null;

        return match ($type) {
            'skill' => $adventurer->hasSlug($slug),
            'item' => $adventurer->hasSlug($slug),
            'gold' => $adventurer->getGold() >= ($condition['quantity'] ?? 0),
            default => true,
        };
    }

    public function consumeCondition(Adventurer $adventurer, ?array $condition): void
    {
        if (!$condition) {
            return;
        }

        $type = $condition['type'] ?? null;
        $slug = $condition['slug'] ?? null;
        $quantity = $condition['quantity'] ?? 1;

        if ($type === 'gold') {
            $adventurer->addGold(-$quantity);
            $this->em->flush();
        } elseif ($type === 'item') {
            $equipment = $this->equipmentRepo->findOneBy(['slug' => $slug]);
            if ($equipment) {
                $this->equipmentService->removeEquipment($adventurer, $equipment, $quantity);
            }
        }
        // skill: on ne consomme pas une discipline
    }

    public function applyPageEvents(Adventurer $adventurer, Page $targetPage): void
    {
        $this->applyEvents($adventurer, $targetPage->getEvents());
    }

    public function applyChoiceEvents(Adventurer $adventurer, Choice $choice): void
    {
        $this->applyEvents($adventurer, $choice->getEvents());
    }

    public function matchDiceRoll(Page $page, int $roll): ?Choice
    {
        foreach ($page->getChoices() as $choice) {
            $text = $choice->getText() ?? '';
            if (!preg_match('/Jet de des:\s*(\d+)(?:-(\d+))?/', $text, $m)) {
                continue;
            }
            $min = (int) $m[1];
            $max = isset($m[2]) ? (int) $m[2] : $min;
            if ($roll >= $min && $roll <= $max) {
                return $choice;
            }
        }

        return null;
    }

    private function applyEvents(Adventurer $adventurer, ?array $events): void
    {
        if (!$events) {
            return;
        }

        foreach ($events['itemsGained'] ?? [] as $item) {
            if (($item['type'] ?? '') === 'gold') {
                $adventurer->addGold($item['quantity'] ?? 1); // addGold est déjà cappé à MAX_GOLD
                continue;
            }
            $equipment = $this->equipmentRepo->findOneBy(['slug' => $item['slug']]);
            if (!$equipment) {
                continue;
            }
            $quantity = $item['quantity'] ?? 1;
            if ($equipment->goesInBackpack()) {
                $remaining = Adventurer::MAX_BACKPACK - $this->equipmentService->countBackpack($adventurer);
                $quantity = min($quantity, $remaining);
                if ($quantity <= 0) {
                    continue;
                }
            }
            $this->equipmentService->addEquipment($adventurer, $equipment, $quantity);
        }

        foreach ($events['itemsLost'] ?? [] as $item) {
            if (($item['type'] ?? '') === 'gold') {
                $adventurer->addGold(-($item['quantity'] ?? 1));
                continue;
            }
            $equipment = $this->equipmentRepo->findOneBy(['slug' => $item['slug']]);
            if ($equipment) {
                $this->equipmentService->removeEquipment($adventurer, $equipment, $item['quantity'] ?? 1);
            }
        }

        $enduranceChange = $events['enduranceChange'] ?? 0;
        if ($enduranceChange !== 0) {
            $adventurer->setEndurance($adventurer->getEndurance() + $enduranceChange);
        }

        $this->em->flush();
    }
}
