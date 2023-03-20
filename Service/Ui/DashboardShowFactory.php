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

use Spipu\DashboardBundle\Entity\DashboardInterface;
use Spipu\DashboardBundle\Service\DashboardViewerService;
use Spipu\DashboardBundle\Service\PeriodService;
use Spipu\DashboardBundle\Service\Ui\Definition\DashboardDefinitionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as Twig;

class DashboardShowFactory
{
    /**
     * @var Twig
     */
    private Twig $twig;

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @var DashboardRouter
     */
    private DashboardRouter $router;

    /**
     * @var PeriodService
     */
    private PeriodService $periodService;

    /**
     * @var DashboardViewerService
     */
    private DashboardViewerService $viewerService;

    /**
     * @var WidgetFactory
     */
    private WidgetFactory $widgetFactory;

    /**
     * GridFactory constructor.
     * @param Twig $twig
     * @param RequestStack $requestStack
     * @param DashboardRouter $router
     * @param PeriodService $periodService
     * @param DashboardViewerService $viewerService
     * @param WidgetFactory $widgetFactory
     */
    public function __construct(
        Twig $twig,
        RequestStack $requestStack,
        DashboardRouter $router,
        PeriodService $periodService,
        DashboardViewerService $viewerService,
        WidgetFactory $widgetFactory
    ) {
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->periodService = $periodService;
        $this->viewerService = $viewerService;
        $this->widgetFactory = $widgetFactory;
    }

    /**
     * @param DashboardDefinitionInterface $dashboardDefinition
     * @param DashboardInterface $dashboard
     * @param DashboardInterface[] $dashboards
     * @return DashboardShowManagerInterface
     */
    public function create(
        DashboardDefinitionInterface $dashboardDefinition,
        DashboardInterface $dashboard,
        array $dashboards
    ): DashboardShowManagerInterface {
        return new DashboardShowManager(
            $this->twig,
            $this->requestStack,
            $this->router,
            $this->periodService,
            $this->viewerService,
            $this->widgetFactory,
            $dashboardDefinition,
            $dashboard,
            $dashboards
        );
    }
}
