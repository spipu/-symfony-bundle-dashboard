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
use Spipu\DashboardBundle\Exception\TypeException;
use Spipu\DashboardBundle\Exception\WidgetException;
use Spipu\DashboardBundle\Service\Ui\Source\DataProvider\DataProviderInterface;
use Spipu\DashboardBundle\Service\WidgetTypeService;
use Spipu\DashboardBundle\Source\SourceDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Twig\Environment as Twig;
use Twig\Error\Error;

/**
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class WidgetManager implements WidgetManagerInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var Twig
     */
    private Twig $twig;

    /**
     * @var WidgetRequest
     */
    private WidgetRequest $request;

    /**
     * @var Widget
     */
    private Widget $definition;

    /**
     * @var DataProviderInterface
     */
    private DataProviderInterface $dataProvider;

    /**
     * @var WidgetTypeService
     */
    private WidgetTypeService $widgetTypeService;

    /**
     * @var string[]
     */
    private array $urls = [
        'refresh' => '',
    ];

    /**
     * @param ContainerInterface $container
     * @param SymfonyRequest $symfonyRequest
     * @param Twig $twig
     * @param WidgetTypeService $widgetTypeService
     * @param Widget $widget
     * @throws SourceException
     */
    public function __construct(
        ContainerInterface $container,
        SymfonyRequest $symfonyRequest,
        Twig $twig,
        WidgetTypeService $widgetTypeService,
        Widget $widget
    ) {
        $this->container = $container;
        $this->twig = $twig;
        $this->definition = $widget;
        $this->widgetTypeService = $widgetTypeService;

        if ($this->definition->getSource()) {
            $this->request = $this->initWidgetRequest($symfonyRequest);
            $this->dataProvider = $this->initDataProvider();
        }
    }

    /**
     * @param SymfonyRequest $symfonyRequest
     * @return WidgetRequest
     */
    private function initWidgetRequest(SymfonyRequest $symfonyRequest): WidgetRequest
    {
        $request = new WidgetRequest($symfonyRequest, $this->definition);
        $request->prepare();

        return $request;
    }

    /**
     * @return DataProviderInterface
     * @throws SourceException
     */
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

    /**
     * @return bool
     * @throws SourceException
     * @throws TypeException
     * @throws WidgetException
     */
    public function validate(): bool
    {
        if ($this->definition->getSource()->hasFilters() && $this->getUrl('refresh') === '') {
            throw new WidgetException('Widget refresh route must be provided');
        }
        $this->loadValues();

        return true;
    }

    /**
     * @return string
     * @throws Error
     */
    public function display(): string
    {
        return $this->twig->render(
            $this->definition->getTemplateAll(),
            [
                'manager' => $this
            ]
        );
    }

    /**
     * @return DataProviderInterface
     */
    public function getDataProvider(): DataProviderInterface
    {
        return $this->dataProvider;
    }

    /**
     * @return Widget
     */
    public function getDefinition(): Widget
    {
        return $this->definition;
    }

    /**
     * @return WidgetRequest
     */
    public function getRequest(): WidgetRequest
    {
        return $this->request;
    }

    /**
     * @return void
     * @throws SourceException
     * @throws TypeException
     */
    private function loadValues(): void
    {
        $this->widgetTypeService->initValues($this);
    }

    /**
     * @param string $code
     * @param string $url
     * @return $this
     */
    public function setUrl(string $code, string $url): self
    {
        $this->urls[$code] = $url;

        return $this;
    }

    /**
     * @param string $code
     * @return string
     */
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
