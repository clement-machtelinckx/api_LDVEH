<?php

namespace App\Repository;

use App\Entity\Adventurer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Adventurer>
 */
class AdventurerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adventurer::class);
    }

    /**
     * Charge un aventurier avec ses équipements et skills en une seule requête.
     */
    public function findWithFullInventory(int $id, ?UserInterface $user = null): ?Adventurer
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.adventurerEquipments', 'ae')->addSelect('ae')
            ->leftJoin('ae.equipment', 'e')->addSelect('e')
            ->leftJoin('a.skills', 's')->addSelect('s')
            ->where('a.id = :id')
            ->setParameter('id', $id);

        if ($user !== null) {
            $qb->andWhere('a.user = :user')
               ->setParameter('user', $user);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}
