<?php

namespace App\Factory;

use App\Entity\Skill;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Skill>
 */
final class SkillFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Skill::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $skillTypes = [
            ['name' => 'Stealth', 'description' => 'Move silently', 'effect' => 'Avoid detection'],
            ['name' => 'Healing', 'description' => 'Heal wounds', 'effect' => 'Restore 2 Endurance'],
            ['name' => 'Combat Mastery', 'description' => 'Expert in combat', 'effect' => '+1 Ability in fights'],
            ['name' => 'Tracking', 'description' => 'Follow trails', 'effect' => 'Detect hidden paths'],
        ];
        
        $skill = self::faker()->randomElement($skillTypes);
        
        return [
            'name' => $skill['name'],
            'description' => $skill['description'],
            'effect' => $skill['effect'],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Skill $skill): void {})
        ;
    }
}
