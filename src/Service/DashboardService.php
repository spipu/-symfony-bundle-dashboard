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

    private EntityManagerInterface $entityManager;
    private DashboardConfigRepository $dashboardRepository;
    private UserIdentifierInterface $userIdentifier;

    public function __construct(
        EntityManagerInterface $entityManager,
        DashboardConfigRepository $dashboardRepository,
        UserIdentifierInterface $userIdentifier
    ) {
        $this->entityManager = $entityManager;
        $this->dashboardRepository = $dashboardRepository;
        $this->userIdentifier = $userIdentifier;
    }

    public function createDefaultDashboard(
        UserInterface $user,
        ?DashboardDefinitionInterface $definition = null
    ): DashboardConfig {
        $dashboard = $this->createDashboard($user, self::DEFAULT_NAME);

        $content = $definition ? $definition->getDefaultConfig() : [];
        $this->completeDefaultConfig($content);

        $dashboard->setContent($content);

        $this->entityManager->flush();

        return $dashboard;
    }

    private function completeDefaultConfig(array &$content): void
    {
        if (!array_key_exists('rows', $content)) {
            return;
        }

        foreach ($content['rows'] as &$row) {
            if (!array_key_exists('cols', $row)) {
                continue;
            }

            foreach ($row['cols'] as &$col) {
                if (!array_key_exists('widgets', $col)) {
                    continue;
                }

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
    }

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

    public function deleteDashboard(DashboardConfig $dashboard, UserInterface $user): void
    {
        if (!$this->canUpdateDashboard($dashboard, $user)) {
            throw new AccessDeniedException('spipu.dashboard.error.not_allowed_to_delete_dashboard');
        }

        $this->entityManager->remove($dashboard);
        $this->entityManager->flush();
    }

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
