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
use Spipu\DashboardBundle\Exception\WidgetException;
use Spipu\DashboardBundle\Service\Ui\Source\DataProvider\DataProviderInterface;
use Spipu\DashboardBundle\Service\Ui\Widget\WidgetRequest;
use Spipu\DashboardBundle\Service\WidgetTypeService;
use Spipu\DashboardBundle\Source\SourceDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as Twig;

/**
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class WidgetManager implements WidgetManagerInterface
{
    private ContainerInterface $container;
    private Twig $twig;
    private WidgetRequest $request;
    private Widget $definition;
    private DataProviderInterface $dataProvider;
    private WidgetTypeService $widgetTypeService;

    private array $urls = [
        'refresh' => '',
    ];

    public function __construct(
        ContainerInterface $container,
        RequestStack $requestStack,
        Twig $twig,
        WidgetTypeService $widgetTypeService,
        Widget $widget
    ) {
        $this->container = $container;
        $this->twig = $twig;
        $this->definition = $widget;
        $this->widgetTypeService = $widgetTypeService;

        if ($this->definition->getSource()) {
            $this->request = $this->initWidgetRequest($requestStack);
            $this->dataProvider = $this->initDataProvider();
        }
    }

    private function initWidgetRequest(RequestStack $requestStack): WidgetRequest
    {
        $request = new WidgetRequest($requestStack, $this->definition);
        $request->prepare();

        return $request;
    }

    private function initDataProvider(): DataProviderInterface
    {
        $dataProvider = clone $this->container->get($this->definition->getSource()->getDataProviderServiceName());
        if (!($dataProvider instanceof DataProviderInterface)) {
            throw new SourceException(printf('The Data Provider must implement %s', DataProviderInterface::class));
        }

        $dataProvider->setSourceRequest($this->request);
        $dataProvider->setSourceDefinition($this->definition->getSource());

        return $dataProvider;
    }

    public function validate(): bool
    {
        if ($this->definition->getSource()->hasFilters() && $this->getUrl('refresh') === '') {
            throw new WidgetException('Widget refresh route must be provided');
        }
        $this->loadValues();

        return true;
    }

    public function display(): string
    {
        return $this->twig->render(
            $this->definition->getTemplateAll(),
            [
                'manager' => $this
            ]
        );
    }

    public function getDataProvider(): DataProviderInterface
    {
        return $this->dataProvider;
    }

    public function getDefinition(): Widget
    {
        return $this->definition;
    }

    public function getRequest(): WidgetRequest
    {
        return $this->request;
    }

    private function loadValues(): void
    {
        $this->widgetTypeService->initValues($this);
    }

    public function setUrl(string $code, string $url): self
    {
        $this->urls[$code] = $url;

        return $this;
    }

    public function getUrl(string $code): string
    {
        return $this->urls[$code];
    }

    /**
     * @param int|float $value
     * @return string
     */
    public function formatValue($value): string
    {
        $nbDecimals = ($this->definition->getSource()->getType() === SourceDefinitionInterface::TYPE_FLOAT ? 2 : 0);

        return number_format((float) $value, $nbDecimals, '.', ' ');
    }
}
