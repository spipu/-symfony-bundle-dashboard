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

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Spipu\DashboardBundle\Service\Ui\DashboardShowFactory;
use Spipu\DashboardBundle\Service\Ui\Definition\DashboardDefinitionInterface;
use Spipu\DashboardBundle\Service\Ui\WidgetFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Throwable;

/**
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 * @SuppressWarnings(PMD.ExcessiveParameterList)
 */
class DashboardControllerService extends AbstractController
{
    private TranslatorInterface $translator;
    private DashboardService $dashboardService;
    private WidgetService $widgetService;
    private RequestStack $requestStack;
    private SourceList $sourceList;
    private PeriodService $periodService;
    private WidgetTypeService $widgetTypeService;
    private EntityManagerInterface $entityManager;
    private DashboardConfiguratorService $dashboardConfiguratorService;
    private DashboardShowFactory $dashboardShowFactory;
    private WidgetFactory $widgetFactory;
    private DashboardDefinitionInterface $dashboardDefinition;

    public function __construct(
        TranslatorInterface $translator,
        DashboardService $dashboardService,
        WidgetService $widgetService,
        RequestStack $requestStack,
        SourceList $sourceList,
        PeriodService $periodService,
        WidgetTypeService $widgetTypeService,
        EntityManagerInterface $entityManager,
        DashboardConfiguratorService $dashboardConfiguratorService,
        DashboardShowFactory $dashboardShowFactory,
        WidgetFactory $widgetFactory
    ) {
        $this->dashboardService = $dashboardService;
        $this->translator = $translator;
        $this->widgetService = $widgetService;
        $this->requestStack = $requestStack;
        $this->sourceList = $sourceList;
        $this->periodService = $periodService;
        $this->widgetTypeService = $widgetTypeService;
        $this->entityManager = $entityManager;
        $this->dashboardConfiguratorService = $dashboardConfiguratorService;
        $this->dashboardShowFactory = $dashboardShowFactory;
        $this->widgetFactory = $widgetFactory;
    }

    public function dispatch(
        DashboardDefinitionInterface $dashboardDefinition,
        string $routeName,
        string $action,
        ?int $id = null
    ): Response {
        $this->dashboardDefinition = $dashboardDefinition;
        $this->dashboardDefinition->getDefinition()->setRoute($routeName);

        switch ($action) {
            case 'create':
                return $this->actionCreate();

            case 'configure':
                return $this->actionConfigure($id);

            case 'delete':
                return $this->actionDelete($id);

            case 'duplicate':
                return $this->actionDuplicate($id);

            case 'save':
                return $this->actionSave($id);

            case '':
            case 'show':
                return $this->actionShow($id);

            case 'refresh_widget':
                return $this->actionRefreshWidget($id);
        }

        throw $this->createNotFoundException('Unknown action');
    }

    protected function actionCreate(): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $dashboardName = trim(strip_tags((string) $this->requestStack->getCurrentRequest()->get('dashboard_name')));
        if (!$dashboardName) {
            $this->addFlashTrans('danger', 'spipu.dashboard.error.name_missing');
            return $this->redirect($this->buildUrl('show'));
        }

        try {
            $dashboard = $this->dashboardService->createDashboard($this->getUser(), $dashboardName);
        } catch (UniqueConstraintViolationException $e) {
            $this->addFlashTrans('danger', 'spipu.dashboard.error.name_already_used');
            return $this->redirect($this->buildUrl('show'));
        }

        return $this->redirect($this->buildUrl('configure', $dashboard->getId()));
    }

    protected function actionConfigure(?int $id): Response
    {
        if ($id === null) {
            throw $this->createNotFoundException('Id is required');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $dashboard = $this->dashboardService->getDashboard($this->getUser(), $id);
        if ($dashboard === null) {
            throw $this->createNotFoundException('Unknown dashboard');
        }

        return $this->render(
            $this->dashboardDefinition->getDefinition()->getTemplateConfigureMain(),
            [
                'definition' => $this->dashboardDefinition->getDefinition(),
                'dashboard'  => $dashboard,
                'sources'    => $this->sourceList->getDefinitions(),
                'periods'    => $this->periodService->getDefinitions(),
                'types'      => $this->widgetTypeService->getDefinitions(),
                'save_url'   => $this->buildUrl('save', $id),
                'delete_url' => $this->buildUrl('delete', $id),
                'back_url'   => $this->buildUrl('show', $id),
            ]
        );
    }

    protected function actionDelete(?int $id): Response
    {
        if ($id === null) {
            throw $this->createNotFoundException('Id is required');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $dashboard = $this->dashboardService->getDashboard($this->getUser(), $id);
        if ($dashboard === null) {
            throw $this->createNotFoundException('Unknown dashboard');
        }

        try {
            $this->dashboardService->deleteDashboard($dashboard, $this->getUser());
        } catch (AccessDeniedException $e) {
            $this->addFlashTrans('danger', $e->getMessage());
        }

        return $this->redirect($this->buildUrl('show'));
    }

    protected function actionDuplicate(?int $id): Response
    {
        if ($id === null) {
            throw $this->createNotFoundException('Id is required');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $dashboard = $this->dashboardService->getDashboard($this->getUser(), $id);
        if ($dashboard === null) {
            throw new NotFoundHttpException('Unknown dashboard');
        }

        $dashboardName = trim(strip_tags((string)$this->requestStack->getCurrentRequest()->get('dashboard_name')));
        if (empty($dashboardName)) {
            $this->addFlashTrans('danger', 'spipu.dashboard.error.name_missing');
            return $this->redirect($this->buildUrl('show', $id));
        }

        try {
            $dashboard = $this->dashboardService->duplicateDashboard($dashboard, $this->getUser(), $dashboardName);
        } catch (UniqueConstraintViolationException $e) {
            $this->addFlashTrans('danger', 'spipu.dashboard.error.name_already_used');
            return $this->redirect($this->buildUrl('show', $id));
        }

        return $this->redirect($this->buildUrl('show', $dashboard->getId()));
    }

    protected function actionSave(?int $id): Response
    {
        if ($id === null) {
            throw $this->createNotFoundException('Id is required');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $configurations = $this->dashboardConfiguratorService->validateAndPrepareConfigurations(
                $this->requestStack->getCurrentRequest()
            );

            $dashboard = $this->dashboardService->getDashboard($this->getUser(), $id);
            if ($dashboard === null) {
                return new JsonResponse(['status' => 'ko', 'message' => 'Unknown dashboard']);
            }

            if ($this->dashboardService->canUpdateDashboard($dashboard, $this->getUser())) {
                $name = trim(strip_tags($this->requestStack->getCurrentRequest()->get('name')));
                $dashboard->setName($name);
            }

            $dashboard->setContent($configurations);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse([
                'status' => 'ko',
                'message' => $this->trans('spipu.dashboard.error.name_already_used')
            ]);
        } catch (Throwable $exception) {
            return new JsonResponse([
                'status' => 'ko',
                'message' => $exception->getMessage()
            ]);
        }

        return new JsonResponse(['status' => 'ok']);
    }

    protected function actionShow(?int $id = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $dashboard = $this->dashboardService->getDashboard($this->getUser(), $id, $this->dashboardDefinition);
        if ($dashboard === null) {
            throw new NotFoundHttpException('Unknown dashboard');
        }
        $id = $dashboard->getId();

        $dashboards = $this->dashboardService->getUserDashboards($this->getUser());

        $manager = $this->dashboardShowFactory->create($this->dashboardDefinition, $dashboard, $dashboards);

        $manager->setUrl('main', $this->buildUrl('show'));
        $manager->setUrl('create', $this->buildUrl('create'));
        $manager->setUrl('reset', $this->buildUrl('show', $id));
        $manager->setUrl('delete', $this->buildUrl('delete', $id));
        $manager->setUrl('duplicate', $this->buildUrl('duplicate', $id));
        $manager->setUrl('configure', $this->buildUrl('configure', $id));
        $manager->setUrl('refresh_widget', $this->buildUrl('refresh_widget', $id));
        $manager->validate();

        return $this->render(
            $this->dashboardDefinition->getDefinition()->getTemplateShowMain(),
            [
                'manager' => $manager
            ]
        );
    }

    protected function actionRefreshWidget(int $id): Response
    {
        $identifier = $this->requestStack->getCurrentRequest()->get('identifier');

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $dashboard = $this->dashboardService->getDashboard($this->getUser(), $id);
        if ($dashboard === null) {
            throw new NotFoundHttpException('Unknown dashboard');
        }
        if ($identifier === null) {
            throw new NotFoundHttpException('Unknown widget');
        }

        $widgetDefinition = $this->dashboardService->getWidgetDefinition($dashboard, $identifier);
        $widget = $this->widgetService->buildWidget($widgetDefinition);
        try {
            $manager = $this->widgetFactory->create($widget);
            $manager->setUrl(
                'refresh',
                $this->buildUrl('refresh_widget', $dashboard->getId(), ['identifier' => $identifier])
            );
            $manager->validate();
        } catch (Exception $exception) {
            $manager = $this->widgetFactory->createError($exception->getMessage(), $widget);
        }

        return $this->render(
            $this->dashboardDefinition->getDefinition()->getTemplateWidgetAll(),
            ['manager' => $manager]
        );
    }

    protected function addFlashTrans(string $type, string $message, array $params = []): void
    {
        $this->addFlash($type, $this->trans($message, $params));
    }

    protected function trans(string $message, array $params = []): string
    {
        return $this->translator->trans($message, $params);
    }

    protected function buildUrl(string $action, ?int $id = null, array $parameters = []): string
    {
        return $this->generateUrl(
            $this->dashboardDefinition->getDefinition()->getRoute(),
            [
                'action' => $action,
                'id' => $id,
            ] + $parameters
        );
    }
}
