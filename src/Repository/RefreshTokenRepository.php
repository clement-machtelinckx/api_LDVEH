<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 */
class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    /**
     * Find a valid (non-expired, non-revoked) refresh token by its hash
     */
    public function findValidByTokenHash(string $hash, \DateTimeImmutable $now): ?RefreshToken
    {
        return $this->createQueryBuilder('rt')
            ->where('rt.tokenHash = :hash')
            ->andWhere('rt.expiresAt > :now')
            ->andWhere('rt.revokedAt IS NULL')
            ->setParameter('hash', $hash)
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
