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
use Spipu\DashboardBundle\Service\WidgetTypeService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as Twig;

class WidgetFactory
{
    private ContainerInterface $container;
    private RequestStack $requestStack;
    private Twig $twig;
    private WidgetTypeService $widgetTypeService;
    private TranslatorInterface $translator;

    public function __construct(
        ContainerInterface $container,
        RequestStack $requestStack,
        Twig $twig,
        WidgetTypeService $widgetTypeService,
        TranslatorInterface $translator
    ) {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
        $this->widgetTypeService = $widgetTypeService;
        $this->translator = $translator;
    }

    public function create(
        Widget $widget
    ): WidgetManager {
        return new WidgetManager(
            $this->container,
            $this->requestStack,
            $this->twig,
            $this->widgetTypeService,
            $this->translator,
            $widget
        );
    }

    public function createError(string $message, Widget $widget): WidgetManager
    {
        $widget
            ->setSourceLabel($message)
            ->setType('error');

        return new WidgetManager(
            $this->container,
            $this->requestStack,
            $this->twig,
            $this->widgetTypeService,
            $this->translator,
            $widget
        );
    }
}
