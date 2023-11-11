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
use Spipu\DashboardBundle\Entity\DashboardAcl;
use Spipu\DashboardBundle\Entity\DashboardInterface;
use Spipu\DashboardBundle\Entity\Widget\Widget;
use Spipu\DashboardBundle\Exception\DashboardException;
use Spipu\DashboardBundle\Exception\SourceException;
use Spipu\DashboardBundle\Service\DashboardViewerService;
use Spipu\DashboardBundle\Service\PeriodService;
use Spipu\DashboardBundle\Service\Ui\Dashboard\DashboardRequest;
use Spipu\DashboardBundle\Service\Ui\Definition\DashboardDefinitionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class DashboardShowManager implements DashboardShowManagerInterface
{
    /**
     * @var Twig
     */
    private Twig $twig;

    /**
     * @var DashboardRouter
     */
    private DashboardRouter $router;

    /**
     * @var DashboardAcl
     */
    private DashboardAcl $dashboardAcl;

    /**
     * @var PeriodService
     */
    private PeriodService $periodService;

    /**
     * @var DashboardViewerService
     */
    private DashboardViewerService $viewerService;

    /**
     * @var DashboardDefinitionInterface
     */
    private DashboardDefinitionInterface $definition;

    /**
     * @var Dashboard|null
     */
    private ?Dashboard $dashboardDefinition = null;

    /**
     * @var DashboardInterface|null
     */
    private ?DashboardInterface $resource;

    /**
     * @var array|DashboardInterface[]
     */
    private array $dashboards;

    /**
     * @var DashboardRequest
     */
    private DashboardRequest $request;

    /**
     * @var Screen
     */
    private Screen $screen;

    /**
     * @var array|WidgetManager[]
     */
    private array $widgetManagers = [];

    /**
     * @var WidgetFactory
     */
    private WidgetFactory $widgetFactory;

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
        $this->request = $this->initDashboardRequest($requestStack);
        $this->widgetFactory = $widgetFactory;
        $this->dashboardAcl = new DashboardAcl();
    }

    /**
     * @param RequestStack $requestStack
     * @return DashboardRequest
     */
    private function initDashboardRequest(
        RequestStack $requestStack
    ): DashboardRequest {
        $request = new DashboardRequest($requestStack->getCurrentRequest(), $this->periodService, $this->resource);
        $request->prepare();

        return $request;
    }

    /**
     * @return bool
     * @throws DashboardException
     * @throws SourceException
     */
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

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function display(): string
    {
        return $this->twig->render(
            $this->dashboardDefinition->getTemplateShowAll(),
            [
                'manager' => $this
            ]
        );
    }

    /**
     * @return void
     */
    private function prepareScreen(): void
    {
        $this->screen = $this->viewerService->buildScreen($this->resource);
    }

    /**
     * @return void
     * @throws SourceException
     */
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

    /**
     * @return DashboardInterface|null
     */
    public function getResource(): ?DashboardInterface
    {
        return $this->resource;
    }

    /**
     * @return Dashboard
     */
    public function getDefinition(): Dashboard
    {
        return $this->dashboardDefinition;
    }

    /**
     * @return array
     */
    public function getDashboards(): array
    {
        return $this->dashboards;
    }

    /**
     * @param string $code
     * @param string $url
     * @return $this
     */
    public function setUrl(string $code, string $url): self
    {
        $this->router->setUrl($code, $url);

        return $this;
    }

    /**
     * @param DashboardAcl $dashboardAcl
     * @return $this
     */
    public function setAcl(DashboardAcl $dashboardAcl): self
    {
        $this->dashboardAcl = $dashboardAcl;

        return $this;
    }

    /**
     * @return DashboardAcl
     */
    public function getAcl(): DashboardAcl
    {
        return $this->dashboardAcl;
    }

    /**
     * @return DashboardRouter
     */
    public function getRouter(): DashboardRouter
    {
        return $this->router;
    }

    /**
     * @return DashboardRequest
     */
    public function getRequest(): DashboardRequest
    {
        return $this->request;
    }

    /**
     * @return string[]
     */
    public function getPeriods(): array
    {
        return $this->periodService->getDefinitions();
    }

    /**
     * @return Screen
     */
    public function getScreen(): Screen
    {
        return $this->screen;
    }

    /**
     * @param Widget $widget
     * @return WidgetManager
     */
    public function getWidgetManager(Widget $widget): WidgetManager
    {
        return $this->widgetManagers[$widget->getId()];
    }
}
