<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spipu\DashboardBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Spipu\DashboardBundle\Entity\DashboardConfig;

/**
 * @method DashboardConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method DashboardConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method DashboardConfig[]    findAll()
 * @method DashboardConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DashboardConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardConfig::class);
    }

    /**
     * @param string $userIdentifier
     * @return DashboardConfig[]
     */
    public function getUserConfigs(string $userIdentifier): array
    {
        return $this->findBy(
            ['userIdentifier' => $userIdentifier],
            ['name' => 'asc']
        );
    }

    public function getUserConfigById(string $userIdentifier, int $gridConfigId): ?DashboardConfig
    {
        return $this->findOneBy(
            [
                'userIdentifier' => $userIdentifier,
                'id' => $gridConfigId,
            ]
        );
    }

    public function getUserConfigByName(string $userIdentifier, string $name): ?DashboardConfig
    {
        return $this->findOneBy(
            [
                'userIdentifier' => $userIdentifier,
                'name' => $name,
            ]
        );
    }
}
