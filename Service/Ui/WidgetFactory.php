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

use Spipu\DashboardBundle\Entity\Widget\Widget;
use Spipu\DashboardBundle\Exception\SourceException;
use Spipu\DashboardBundle\Service\WidgetTypeService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as Twig;

class WidgetFactory
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @var Twig
     */
    private Twig $twig;
    /**
     * @var WidgetTypeService
     */
    private WidgetTypeService $widgetTypeService;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param Twig $twig
     * @param WidgetTypeService $widgetTypeService
     */
    public function __construct(
        ContainerInterface $container,
        RequestStack $requestStack,
        Twig $twig,
        WidgetTypeService $widgetTypeService
    ) {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
        $this->widgetTypeService = $widgetTypeService;
    }

    /**
     * @param Widget $widget
     * @return WidgetManager
     * @throws SourceException
     */
    public function create(
        Widget $widget
    ): WidgetManager {
        return new WidgetManager(
            $this->container,
            $this->requestStack->getCurrentRequest(),
            $this->twig,
            $this->widgetTypeService,
            $widget
        );
    }

    /**
     * @param string $message
     * @param string $id
     * @return WidgetManager
     * @throws SourceException
     */
    public function createError(string $message, string $id): WidgetManager
    {
        $widget = new Widget($id);
        $widget
            ->setSourceLabel($message)
            ->setType('error');

        return new WidgetManager(
            $this->container,
            $this->requestStack->getCurrentRequest(),
            $this->twig,
            $this->widgetTypeService,
            $widget
        );
    }
}
