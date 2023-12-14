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

use Spipu\DashboardBundle\Entity\Widget\Widget;
use Spipu\DashboardBundle\Exception\SourceException;

class WidgetService
{
    private SourceList $sourceList;
    private PeriodService $periodService;

    public function __construct(
        SourceList $sourceList,
        PeriodService $periodService
    ) {
        $this->sourceList = $sourceList;
        $this->periodService = $periodService;
    }

    public function buildWidget(array $definitionWidget): ?Widget
    {
        $id = (string)$definitionWidget['id'];
        $sourceCode = (string)$definitionWidget['source'];
        $widgetType = (string)$definitionWidget['type'];
        $height = (int)$definitionWidget['height'];
        $periodType = $definitionWidget['period'];
        $filters = $definitionWidget['filters'] ?? [];

        try {
            $source = $this->sourceList->getSource($sourceCode);
        } catch (SourceException $exception) {
            return null;
        }

        $sourceLabel = $this->sourceList->getSourceLabel($source);
        if ($source->getDefinition()->getDateField() === null && $periodType === null) {
            $periodType = 'hour';
        }
        $period = $this->periodService->create($periodType);

        $widget = new Widget($id);
        $widget
            ->setSource($source->getDefinition())
            ->setSourceLabel($sourceLabel)
            ->setType($widgetType)
            ->setPeriod($period)
            ->setHeight($height)
            ->setFilters($filters);

        return $widget;
    }
}
