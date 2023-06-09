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

namespace Spipu\DashboardBundle\Service\Ui;

use Exception;
use Spipu\DashboardBundle\Entity\Dashboard\Dashboard;
use Spipu\DashboardBundle\Entity\Dashboard\Screen;
use Spipu\DashboardBundle\Entity\DashboardInterface;
use Spipu\DashboardBundle\Entity\Widget\Widget;
use Spipu\DashboardBundle\Exception\DashboardException;
use Spipu\DashboardBundle\Service\DashboardViewerService;
use Spipu\DashboardBundle\Service\PeriodService;
use Spipu\DashboardBundle\Service\Ui\Dashboard\DashboardRequest;
use Spipu\DashboardBundle\Service\Ui\Definition\DashboardDefinitionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as Twig;

/**
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class DashboardShowManager implements DashboardShowManagerInterface
{
    private Twig $twig;
    private DashboardRouter $router;
    private PeriodService $periodService;
    private DashboardViewerService $viewerService;
    private DashboardDefinitionInterface $definition;
    private ?Dashboard $dashboardDefinition = null;
    private ?DashboardInterface $resource;
    private array $dashboards;
    private DashboardRequest $request;
    private Screen $screen;
    private WidgetFactory $widgetFactory;

    /**
     * @var WidgetManager[]
     */
    private array $widgetManagers = [];


    /**
     * @param Twig $twig
     * @param RequestStack $requestStack
     * @param DashboardRouter $router
     * @param PeriodService $periodService
     * @param DashboardViewerService $viewerService
     * @param WidgetFactory $widgetFactory
     * @param DashboardDefinitionInterface $definition
     * @param DashboardInterface $resource
     * @param DashboardInterface[] $dashboards
     * @SuppressWarnings(PMD.ExcessiveParameterList)
     */
    public function __construct(
        Twig $twig,
        RequestStack $requestStack,
        DashboardRouter $router,
        PeriodService $periodService,
        DashboardViewerService $viewerService,
        WidgetFactory $widgetFactory,
        DashboardDefinitionInterface $definition,
        DashboardInterface $resource,
        array $dashboards
    ) {
        $this->twig = $twig;
        $this->router = $router;
        $this->periodService = $periodService;
        $this->viewerService = $viewerService;
        $this->definition = $definition;
        $this->resource = $resource;
        $this->dashboards = $dashboards;
        $this->widgetFactory = $widgetFactory;
        $this->request = $this->initDashboardRequest($requestStack);
    }

    private function initDashboardRequest(
        RequestStack $requestStack
    ): DashboardRequest {
        $request = new DashboardRequest($requestStack, $this->periodService, $this->resource);
        $request->prepare();

        return $request;
    }

    public function validate(): bool
    {
        if (!$this->resource) {
            throw new DashboardException('The Show Manager is not ready');
        }

        $this->dashboardDefinition = $this->definition->getDefinition();

        $this->prepareScreen();
        $this->prepareWidgetManagers();

        return true;
    }

    public function display(): string
    {
        return $this->twig->render(
            $this->dashboardDefinition->getTemplateShowAll(),
            [
                'manager' => $this
            ]
        );
    }

    private function prepareScreen(): void
    {
        $this->screen = $this->viewerService->buildScreen($this->resource);
    }

    private function prepareWidgetManagers(): void
    {
        foreach ($this->screen->getWidgets() as $widget) {
            try {
                $widgetManager = $this->widgetFactory->create($widget);
                $widgetManager->setUrl(
                    'refresh',
                    $this->router->getUrl('refresh_widget') . '?' . http_build_query(['identifier' => $widget->getId()])
                );
                $widgetManager->getRequest()->setPeriod($this->request->getPeriod());
                $widgetManager->validate();
            } catch (Exception $exception) {
                $widgetManager = $this->widgetFactory->createError($exception->getMessage(), $widget);
            }
            $this->widgetManagers[$widget->getId()] = $widgetManager;
        }
    }

    public function getResource(): ?DashboardInterface
    {
        return $this->resource;
    }

    public function getDefinition(): Dashboard
    {
        return $this->dashboardDefinition;
    }

    public function getDashboards(): array
    {
        return $this->dashboards;
    }

    public function setUrl(string $code, string $url): self
    {
        $this->router->setUrl($code, $url);

        return $this;
    }

    public function getRouter(): DashboardRouter
    {
        return $this->router;
    }

    public function getRequest(): DashboardRequest
    {
        return $this->request;
    }

    public function getPeriods(): array
    {
        return $this->periodService->getDefinitions();
    }

    public function getScreen(): Screen
    {
        return $this->screen;
    }

    public function getWidgetManager(Widget $widget): WidgetManager
    {
        return $this->widgetManagers[$widget->getId()];
    }
}
