<?php

namespace App\Service;

use App\Entity\Page;
use App\Entity\Monster;
use App\Entity\Adventurer;
use App\Entity\FightHistory;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FightHistoryRepository;

class CombatService
{
    public function __construct(
        private FightHistoryRepository $fightHistoryRepo,
        private EntityManagerInterface $em
    ) {}

    /** Colonnes de QA “bandées” pour la table */
    private const QA_BANDS = [-11, -10, -8, -6, -4, -2, 0, 1, 3, 5, 7, 9, 11];

    /**
     * Table des coups portés (ligne=jet 0..9, colonne=bande QA) :
     * valeur: ['ls'=>perteLS,'e'=>perteEnnemi] ou ['t'=>true] (ennemi tué)
     * NB: 0 RESTE 0 ici (on n’applique pas 0->10 pour la table).
     */
    private const TABLE = [
        0 => [-11=>['ls'=>0,'e'=>6], -10=>['ls'=>0,'e'=>7], -8=>['ls'=>0,'e'=>8], -6=>['ls'=>0,'e'=>9], -4=>['ls'=>0,'e'=>10], -2=>['ls'=>0,'e'=>11], 0=>['ls'=>0,'e'=>12], 1=>['ls'=>0,'e'=>14], 3=>['ls'=>0,'e'=>16], 5=>['ls'=>0,'e'=>18], 7=>['t'=>true], 9=>['t'=>true], 11=>['t'=>true]],
        1 => [-11=>['ls'=>10,'e'=>0], -10=>['ls'=>10,'e'=>0], -8=>['ls'=>8,'e'=>0], -6=>['ls'=>6,'e'=>0], -4=>['ls'=>6,'e'=>1], -2=>['ls'=>5,'e'=>2], 0=>['ls'=>5,'e'=>3], 1=>['ls'=>5,'e'=>4], 3=>['ls'=>5,'e'=>4], 5=>['ls'=>4,'e'=>6], 7=>['ls'=>4,'e'=>7], 9=>['ls'=>3,'e'=>8], 11=>['ls'=>3,'e'=>9]],
        2 => [-11=>['ls'=>10,'e'=>0], -10=>['ls'=>8,'e'=>0], -8=>['ls'=>7,'e'=>0], -6=>['ls'=>6,'e'=>1], -4=>['ls'=>5,'e'=>2], -2=>['ls'=>5,'e'=>3], 0=>['ls'=>4,'e'=>4], 1=>['ls'=>4,'e'=>5], 3=>['ls'=>3,'e'=>6], 5=>['ls'=>3,'e'=>7], 7=>['ls'=>3,'e'=>8], 9=>['ls'=>3,'e'=>9], 11=>['ls'=>2,'e'=>10]],
        3 => [-11=>['ls'=>8,'e'=>0], -10=>['ls'=>7,'e'=>0], -8=>['ls'=>6,'e'=>1], -6=>['ls'=>5,'e'=>2], -4=>['ls'=>5,'e'=>3], -2=>['ls'=>4,'e'=>4], 0=>['ls'=>4,'e'=>5], 1=>['ls'=>3,'e'=>6], 3=>['ls'=>3,'e'=>7], 5=>['ls'=>3,'e'=>8], 7=>['ls'=>2,'e'=>9], 9=>['ls'=>2,'e'=>10], 11=>['ls'=>2,'e'=>11]],
        4 => [-11=>['ls'=>8,'e'=>0], -10=>['ls'=>7,'e'=>1], -8=>['ls'=>6,'e'=>2], -6=>['ls'=>5,'e'=>3], -4=>['ls'=>4,'e'=>4], -2=>['ls'=>4,'e'=>5], 0=>['ls'=>3,'e'=>6], 1=>['ls'=>3,'e'=>7], 3=>['ls'=>2,'e'=>8], 5=>['ls'=>2,'e'=>9], 7=>['ls'=>2,'e'=>10], 9=>['ls'=>2,'e'=>11], 11=>['ls'=>2,'e'=>12]],
        5 => [-11=>['ls'=>7,'e'=>1], -10=>['ls'=>6,'e'=>2], -8=>['ls'=>5,'e'=>3], -6=>['ls'=>4,'e'=>4], -4=>['ls'=>4,'e'=>5], -2=>['ls'=>3,'e'=>6], 0=>['ls'=>2,'e'=>7], 1=>['ls'=>2,'e'=>8], 3=>['ls'=>2,'e'=>9], 5=>['ls'=>2,'e'=>10], 7=>['ls'=>2,'e'=>11], 9=>['ls'=>2,'e'=>12], 11=>['ls'=>1,'e'=>14]],
        6 => [-11=>['ls'=>6,'e'=>2], -10=>['ls'=>6,'e'=>3], -8=>['ls'=>5,'e'=>4], -6=>['ls'=>4,'e'=>5], -4=>['ls'=>3,'e'=>6], -2=>['ls'=>2,'e'=>7], 0=>['ls'=>2,'e'=>8], 1=>['ls'=>2,'e'=>9], 3=>['ls'=>2,'e'=>10], 5=>['ls'=>1,'e'=>11], 7=>['ls'=>1,'e'=>12], 9=>['ls'=>1,'e'=>14], 11=>['ls'=>1,'e'=>16]],
        7 => [-11=>['ls'=>5,'e'=>3], -10=>['ls'=>5,'e'=>4], -8=>['ls'=>4,'e'=>5], -6=>['ls'=>3,'e'=>6], -4=>['ls'=>2,'e'=>7], -2=>['ls'=>2,'e'=>8], 0=>['ls'=>1,'e'=>9], 1=>['ls'=>1,'e'=>10], 3=>['ls'=>1,'e'=>11], 5=>['ls'=>0,'e'=>12], 7=>['ls'=>0,'e'=>14], 9=>['ls'=>0,'e'=>16], 11=>['ls'=>0,'e'=>18]],
        8 => [-11=>['ls'=>4,'e'=>4], -10=>['ls'=>4,'e'=>5], -8=>['ls'=>3,'e'=>6], -6=>['ls'=>2,'e'=>7], -4=>['ls'=>1,'e'=>8], -2=>['ls'=>1,'e'=>9], 0=>['ls'=>0,'e'=>10], 1=>['ls'=>0,'e'=>11], 3=>['ls'=>0,'e'=>12], 5=>['ls'=>0,'e'=>14], 7=>['ls'=>0,'e'=>16], 9=>['ls'=>0,'e'=>18], 11=>['t'=>true]],
        9 => [-11=>['ls'=>3,'e'=>5], -10=>['ls'=>0,'e'=>7], -8=>['ls'=>2,'e'=>7], -6=>['ls'=>0,'e'=>8], -4=>['ls'=>0,'e'=>9], -2=>['ls'=>0,'e'=>10], 0=>['ls'=>0,'e'=>11], 1=>['ls'=>0,'e'=>12], 3=>['ls'=>0,'e'=>14], 5=>['ls'=>0,'e'=>16], 7=>['ls'=>0,'e'=>18], 9=>['t'=>true], 11=>['t'=>true]],
    ];

    /**
     * Combat complet jusqu’à la mort de l’un des deux.
     * - garde l’API existante (retourne 'adventurer'/'monster'/'log'…)
     * - enregistre la victoire de l’aventurier comme avant
     * - ajoute un 'history' détaillé par assaut
     */
    public function fight(Adventurer $adventurer, Monster $monster): array
    {
        $playerName = method_exists($adventurer, 'getAdventurerName') ? $adventurer->getAdventurerName() : 'Loup Solitaire';
        $enemyName  = method_exists($monster, 'getMonsterName') ? $monster->getMonsterName() : 'Ennemi';

        $playerHab  = (int) $adventurer->getAbility();
        $enemyHab   = (int) $monster->getAbility();

        $playerEnd  = $this->readEndurance($adventurer);
        $enemyEnd   = $this->readEndurance($monster);

        $bonusHab   = 0; // pour l’instant pas d’équipement/discipline

        $history = [];
        $round   = 1;
        $maxRounds = 200; // garde-fou

        while ($round <= $maxRounds) {
            $qa   = ($playerHab + $bonusHab) - $enemyHab;
            $band = $this->mapQaToBand($qa);

            $roll = random_int(0, 9);

            $cell = self::TABLE[$roll][$band] ?? null;
            if (!$cell) {
                throw new \RuntimeException("Cellule table manquante (roll=$roll, band=$band)");
            }

            $t = isset($cell['t']) && $cell['t'] === true;

            $playerLoss = $t ? 0 : (int)($cell['ls'] ?? 0);
            $enemyLoss  = $t ? $enemyEnd : (int)($cell['e'] ?? 0);
            $kill = !empty($cell['t']);

            if ($kill) {
                $enemyEnd = 0;
                // par convention Lone Wolf: T n’inflige pas de pertes à LS si non spécifié
                $playerLoss = (int)($cell['ls'] ?? 0);
            } else {
                $enemyEnd  = max(0, $enemyEnd - $enemyLoss);
            }
            $playerEnd = max(0, $playerEnd - $playerLoss);

            // Persiste la vie du joueur à chaque round
            if ($this->writeEndurance($adventurer, $playerEnd)) {
                $this->em->persist($adventurer);
                $this->em->flush(); // => commit round par round
            }

            $history[] = [
                'round'      => $round,
                'roll'       => $roll,
                'qa'         => $qa,
                'band'       => $band,
                'playerLoss' => $playerLoss,
                'enemyLoss'  => $enemyLoss,
                't'          => $t,
                'playerEnd'  => $playerEnd,
                'enemyEnd'   => $enemyEnd,
            ];

            if ($playerEnd <= 0 || $enemyEnd <= 0) {
                break;
            }
            ++$round;
        }

        // résolution vainqueur pour compat avec ton API
        $winner = null;
        if ($playerEnd > 0 && $enemyEnd <= 0) {
            $winner = 'adventurer';
            $this->recordFight($adventurer, $monster, true);
        } elseif ($enemyEnd > 0 && $playerEnd <= 0) {
            $winner = 'monster';
        } else {
            // double KO ou maxRounds atteint (peu probable)
            $winner = null;
        }

        // on garde la forme “legacy” (base/roll/total) pour éviter de casser le front
        // => on met les infos du DERNIER assaut
        $last = end($history) ?: null;
        $legacyAdventurerRoll = $last ? $last['roll'] : 0;
        $legacyMonsterRoll    = $legacyAdventurerRoll; // juste décoratif, l’attaque est simultanée dans la table

        return [
            'adventurer' => [
                'adventurerName' => $playerName,
                'base'  => $playerHab,
                'roll'  => $legacyAdventurerRoll, // décoratif pour le front actuel
                'total' => $playerHab,            // idem
                'endurance' => $playerEnd,
            ],
            'monster' => [
                'monsterName' => $enemyName,
                'base'  => $enemyHab,
                'roll'  => $legacyMonsterRoll,
                'total' => $enemyHab,
                'endurance' => $enemyEnd,
            ],
            'winner'  => $winner,
            'log'     => sprintf(
                'Combat en %d assaut(s) — QA=%d (%s) — Vainqueur: %s',
                count($history),
                $last['qa'] ?? (($playerHab + $bonusHab) - $enemyHab),
                $playerName,
                $winner ? ucfirst($winner) : 'aucun'
            ),
            'history' => $history, // détails tour par tour
        ];
    }

    public function canAccessPage(Page $page, Adventurer $adventurer): bool
    {
        $monster = $page->getMonster();
        if (!$monster || !$page->isCombatIsBlocking()) {
            return true;
        }
        return $this->hasDefeated($adventurer, $monster);
    }

    public function hasDefeated(Adventurer $adventurer, Monster $monster): bool
    {
        return $this->fightHistoryRepo->findOneBy([
            'adventurer' => $adventurer,
            'monster'    => $monster,
            'victory'    => true,
        ]) !== null;
    }

    public function recordFight(Adventurer $adventurer, Monster $monster, bool $victory): void
    {
        $fightHistory = new FightHistory();
        $fightHistory->setAdventurer($adventurer);
        $fightHistory->setMonster($monster);
        $fightHistory->setVictory($victory);

        $this->em->persist($fightHistory);
        $this->em->flush();
    }

    /** Mappe QA brut vers bande de colonne de la table */
    private function mapQaToBand(int $qa): int
    {
        if ($qa <= -11) return -11;
        if ($qa <= -9)  return -10; // -10/-9
        if ($qa <= -7)  return -8;  // -8/-7
        if ($qa <= -5)  return -6;  // -6/-5
        if ($qa <= -3)  return -4;  // -4/-3
        if ($qa <= -1)  return -2;  // -2/-1
        if ($qa == 0)   return 0;
        if ($qa <= 2)   return 1;   // +1/+2
        if ($qa <= 4)   return 3;   // +3/+4
        if ($qa <= 6)   return 5;   // +5/+6
        if ($qa <= 8)   return 7;   // +7/+8
        if ($qa <= 10)  return 9;   // +9/+10
        return 11;                   // +11 ou plus
    }

    /** Lis l’ENDURANCE/Hp probable sans casser si la méthode diffère selon l’entité */
    private function readEndurance(object $o): int
    {
        foreach (['getEndurance', 'getCurrentEndurance', 'getHp', 'getHealth'] as $m) {
            if (method_exists($o, $m)) {
                return (int) $o->{$m}();
            }
        }
        throw new \RuntimeException('Méthode Endurance/HP introuvable sur '.get_class($o));
    }

    // + helper générique pour setter l’ENDURANCE quel que soit le nom du champ
    private function writeEndurance(object $o, int $value): bool
    {
        // essaie les setters courants
        foreach (['setCurrentEndurance', 'setEndurance', 'setHp', 'setHealth'] as $setter) {
            if (method_exists($o, $setter)) {
                // lit la valeur actuelle pour éviter les flush inutiles
                $current = $this->readEndurance($o);
                if ($current !== $value) {
                    $o->{$setter}($value);
                    return true;
                }
                return false;
            }
        }
        throw new \RuntimeException('Setter Endurance/HP introuvable sur '.get_class($o));
    }


    /**
     * Tirage “init” 1..10 avec 0 ≡ 10 (si tu en as besoin ailleurs).
     * Pour COMBAT on utilise random_int(0,9) tel quel.
     */
    private function rollInitZeroIsTen(): int
    {
        $d = random_int(0, 9);
        return $d === 0 ? 10 : $d;
    }
}
