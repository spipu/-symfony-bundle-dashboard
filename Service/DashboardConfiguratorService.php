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

use Spipu\DashboardBundle\Exception\SourceException;
use Spipu\DashboardBundle\Exception\TypeException;
use Spipu\DashboardBundle\Exception\WidgetException;
use Spipu\DashboardBundle\Source\SourceDefinitionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @SuppressWarnings(PMD.ExcessiveClassComplexity)
 */
class DashboardConfiguratorService
{
    /**
     * @var SourceList
     */
    private SourceList $sourceList;

    /**
     * @var PeriodService
     */
    private PeriodService $periodService;

    /**
     * @var WidgetTypeService
     */
    private WidgetTypeService $widgetTypeService;

    /**
     * @param SourceList $sourceList
     * @param PeriodService $periodService
     * @param WidgetTypeService $widgetTypeService
     */
    public function __construct(
        SourceList $sourceList,
        PeriodService $periodService,
        WidgetTypeService $widgetTypeService
    ) {
        $this->sourceList = $sourceList;
        $this->periodService = $periodService;
        $this->widgetTypeService = $widgetTypeService;
    }

    /**
     * @param Request $request
     * @return array
     * @throws WidgetException
     */
    public function validateAndPrepareConfigurations(Request $request): array
    {
        $requestData = $request->request->get('configurations');
        if (!is_string($requestData)) {
            throw $this->createException('Bad format data');
        }

        $configurations = json_decode($requestData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw $this->createException('Bad format json');
        }

        if (!array_key_exists('rows', $configurations)) {
            throw $this->createException('rows is required', $configurations);
        }

        $configurations['rows'] = array_values($configurations['rows']);
        foreach ($configurations['rows'] as &$confRow) {
            $this->checkConfigurationsDataRow($confRow);
        }

        return $configurations;
    }

    /**
     * @param array $confRow
     * @return void
     * @throws WidgetException
     */
    private function checkConfigurationsDataRow(array &$confRow): void
    {
        unset($confRow['id'], $confRow['width']);

        if (!array_key_exists('title', $confRow) || strlen($confRow['title']) > 255) {
            throw $this->createException('row - title invalid', $confRow);
        }

        if (
            !array_key_exists('nbCol', $confRow)
            || !is_int($confRow['nbCol'])
            || !array_key_exists('cols', $confRow)
            || !is_array($confRow['cols'])
            || count($confRow['cols']) !== $confRow['nbCol']
        ) {
            throw $this->createException('row - cols count invalid', $confRow);
        }

        $confRow['cols'] = array_values($confRow['cols']);
        foreach ($confRow['cols'] as &$confCol) {
            $this->checkConfigurationsDataCol($confCol);
        }
    }

    /**
     * @param array $confCol
     * @return void
     * @throws WidgetException
     */
    private function checkConfigurationsDataCol(array &$confCol): void
    {
        unset($confCol['id']);

        if (!isset($confCol['widgets']) || !is_array($confCol['widgets'])) {
            throw $this->createException('col - bad format for widgets', $confCol);
        }

        $confCol['widgets'] = array_values(array_filter($confCol['widgets']));
        foreach ($confCol['widgets'] as $confWidget) {
            $this->checkConfigurationsDataWidget($confWidget);
        }
    }

    /**
     * @param array $confWidget
     * @return void
     * @throws WidgetException
     */
    private function checkConfigurationsDataWidget(array $confWidget): void
    {
        if (!array_key_exists('width', $confWidget) || !is_int($confWidget['width']) || $confWidget['width'] > 4) {
            throw $this->createException('widget - bad width', $confWidget);
        }

        if (!array_key_exists('height', $confWidget) || !is_int($confWidget['height']) || $confWidget['height'] > 2) {
            throw $this->createException('widget - bad height', $confWidget);
        }

        $source = $this->checkConfigurationsDataWidgetSource($confWidget);
        $type = $this->checkConfigurationsDataWidgetType($confWidget);
        $period = $this->checkConfigurationsDataWidgetPeriod($confWidget);

        $this->checkConfigurationsDataWidgetCoherency($source, $type, $period, $confWidget);
    }

    /**
     * @param array $confWidget
     * @return SourceDefinitionInterface
     * @throws WidgetException
     */
    private function checkConfigurationsDataWidgetSource(array $confWidget): SourceDefinitionInterface
    {
        if (!array_key_exists('source', $confWidget) || !is_string($confWidget['source'])) {
            throw $this->createException('widget - bad source', $confWidget);
        }

        try {
            $source = $this->sourceList->getSource($confWidget['source']);
        } catch (SourceException $e) {
            $source = null;
        }

        if (!$source) {
            throw $this->createException('widget - undefined source', $confWidget);
        }

        return $source;
    }

    /**
     * @param array $confWidget
     * @return array
     * @throws WidgetException
     */
    private function checkConfigurationsDataWidgetType(array $confWidget): array
    {
        if (!array_key_exists('type', $confWidget) || !is_string($confWidget['type'])) {
            throw $this->createException('widget - bad type', $confWidget);
        }

        try {
            $type = $this->widgetTypeService->getDefinition($confWidget['type']);
        } catch (TypeException $e) {
            $type = null;
        }

        if (!$type) {
            throw $this->createException('widget - undefined type', $confWidget);
        }

        return $type;
    }

    /**
     * @param array $confWidget
     * @return string|null
     * @throws WidgetException
     */
    private function checkConfigurationsDataWidgetPeriod(array $confWidget): ?string
    {
        if (!array_key_exists('period', $confWidget)) {
            throw $this->createException('widget - bad period', $confWidget);
        }

        if (!is_string($confWidget['period']) && $confWidget['period'] !== null) {
            throw $this->createException('widget - bad period', $confWidget);
        }

        if ($confWidget['period'] && !in_array($confWidget['period'], $this->periodService->getTypes())) {
            throw $this->createException('widget - bad period', $confWidget);
        }

        return $confWidget['period'];
    }

    /**
     * @param SourceDefinitionInterface $source
     * @param array $type
     * @param string|null $period
     * @param array $confWidget
     * @return void
     * @throws WidgetException
     */
    protected function checkConfigurationsDataWidgetCoherency(
        SourceDefinitionInterface $source,
        array $type,
        ?string $period,
        array $confWidget
    ): void {
        if (!$source->getDefinition()->getDateField() && $type['needPeriod']) {
            throw $this->createException('widget - source need type without period', $confWidget);
        }

        if ($source->getDefinition()->getDateField() && !$period) {
            throw $this->createException('widget - source need period', $confWidget);
        }

        if (!$source->getDefinition()->getDateField() && $period) {
            throw $this->createException('widget - source does not need period', $confWidget);
        }
    }

    /**
     * @param string $message
     * @param array|null $configuration
     * @return WidgetException
     */
    private function createException(string $message, ?array $configuration = null): WidgetException
    {
        if ($configuration) {
            $message .= ' - ' . print_r($configuration, true);
        }

        return new WidgetException($message);
    }
}
