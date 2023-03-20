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

namespace Spipu\DashboardBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Spipu\DashboardBundle\Entity\DashboardConfig;
use Spipu\DashboardBundle\Exception\WidgetException;
use Spipu\DashboardBundle\Repository\DashboardConfigRepository;
use Spipu\DashboardBundle\Service\Ui\Definition\DashboardDefinitionInterface;
use Spipu\UiBundle\Service\Ui\Grid\UserIdentifierInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class DashboardService
{
    public const DEFAULT_NAME = 'default';

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var DashboardConfigRepository
     */
    private DashboardConfigRepository $dashboardRepository;

    /**
     * @var UserIdentifierInterface
     */
    private UserIdentifierInterface $userIdentifier;

    /**
     * @param EntityManagerInterface $entityManager
     * @param DashboardConfigRepository $dashboardRepository
     * @param UserIdentifierInterface $userIdentifier
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        DashboardConfigRepository $dashboardRepository,
        UserIdentifierInterface $userIdentifier
    ) {
        $this->entityManager = $entityManager;
        $this->dashboardRepository = $dashboardRepository;
        $this->userIdentifier = $userIdentifier;
    }

    /**
     * @param UserInterface $user
     * @param DashboardDefinitionInterface|null $definition
     * @return DashboardConfig
     */
    public function createDefaultDashboard(
        UserInterface $user,
        ?DashboardDefinitionInterface $definition = null
    ): DashboardConfig {
        $dashboard = $this->createDashboard($user, self::DEFAULT_NAME);

        $content = $definition ? $definition->getDefaultConfig() : [];
        foreach ($content['rows'] as &$row) {
            foreach ($row['cols'] as &$col) {
                foreach ($col['widgets'] as &$widget) {
                    if (!array_key_exists('id', $widget)) {
                        $widget['id'] = uniqid();
                    }
                    if (!array_key_exists('filters', $widget)) {
                        $widget['filters'] = [];
                    }
                }
            }
        }

        $dashboard->setContent($content);

        $this->entityManager->flush();

        return $dashboard;
    }

    /**
     * @param UserInterface $user
     * @param string $dashboardName
     * @return DashboardConfig
     */
    public function createDashboard(UserInterface $user, string $dashboardName): DashboardConfig
    {
        $dashboard = new DashboardConfig();

        $dashboard->setCreatedAtValue();
        $dashboard->setUpdatedAtValue();
        $dashboard->setName($dashboardName);
        $dashboard->setUserIdentifier($this->userIdentifier->getIdentifier($user));

        $this->entityManager->persist($dashboard);
        $this->entityManager->flush();

        return $dashboard;
    }

    /**
     * @param DashboardConfig $dashboard
     * @param UserInterface $user
     * @param string $name
     * @return DashboardConfig
     */
    public function duplicateDashboard(DashboardConfig $dashboard, UserInterface $user, string $name): DashboardConfig
    {
        $dashboardDuplicated = new DashboardConfig();

        $dashboardDuplicated->setName($name);
        $dashboardDuplicated->setUserIdentifier($this->userIdentifier->getIdentifier($user));
        $dashboardDuplicated->setContent($dashboard->getContent());

        $this->entityManager->persist($dashboardDuplicated);
        $this->entityManager->flush();

        return $dashboardDuplicated;
    }

    /**
     * @param DashboardConfig $dashboard
     * @param UserInterface $user
     * @return void
     */
    public function deleteDashboard(DashboardConfig $dashboard, UserInterface $user): void
    {
        if (!$this->canUpdateDashboard($dashboard, $user)) {
            throw new AccessDeniedException('spipu.dashboard.error.not_allowed_to_delete_dashboard');
        }

        $this->entityManager->remove($dashboard);
        $this->entityManager->flush();
    }

    /**
     * @param UserInterface $user
     * @param int|null $id
     * @param DashboardDefinitionInterface|null $definition
     * @return DashboardConfig|null
     */
    public function getDashboard(
        UserInterface $user,
        ?int $id,
        ?DashboardDefinitionInterface $definition = null
    ): ?DashboardConfig {
        if ($id === null) {
            return $this->getDefaultDashboard($user, $definition);
        }

        return $this->dashboardRepository->findOneBy(
            [
                'userIdentifier' => $this->userIdentifier->getIdentifier($user),
                'id' => $id
            ]
        );
    }

    /**
     * @param UserInterface $user
     * @param DashboardDefinitionInterface|null $definition
     * @return DashboardConfig
     */
    public function getDefaultDashboard(
        UserInterface $user,
        ?DashboardDefinitionInterface $definition = null
    ): DashboardConfig {
        $dashboard = $this->dashboardRepository->findOneBy([
            'userIdentifier' => $this->userIdentifier->getIdentifier($user),
            'name' => self::DEFAULT_NAME
        ]);

        if (!$dashboard) {
            $dashboard = $this->createDefaultDashboard($user, $definition);
        }

        return $dashboard;
    }

    /**
     * @param DashboardConfig $dashboard
     * @param UserInterface $user
     * @return bool
     */
    public function canUpdateDashboard(DashboardConfig $dashboard, UserInterface $user): bool
    {
        return (
            $this->userIdentifier->getIdentifier($user) === $dashboard->getUserIdentifier()
            && $dashboard->getName() !== self::DEFAULT_NAME
        );
    }

    /**
     * @param UserInterface $user
     * @return DashboardConfig[]
     */
    public function getUserDashboards(UserInterface $user): array
    {
        return $this->dashboardRepository->getUserConfigs($this->userIdentifier->getIdentifier($user));
    }

    /**
     * @param DashboardConfig $dashboard
     * @param string $identifier
     * @return array
     * @throws WidgetException
     */
    public function getWidgetDefinition(DashboardConfig $dashboard, string $identifier): array
    {
        $definition = $dashboard->getContent();
        foreach ($definition['rows'] as $row) {
            foreach ($row['cols'] as $col) {
                foreach ($col['widgets'] as $widget) {
                    if ($widget['id'] === $identifier) {
                        return $widget;
                    }
                }
            }
        }
        throw new WidgetException('Widget definition not found');
    }
}
