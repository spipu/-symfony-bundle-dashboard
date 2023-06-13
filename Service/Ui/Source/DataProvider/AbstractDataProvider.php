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

namespace Spipu\DashboardBundle\Service\Ui\Source\DataProvider;

use Spipu\DashboardBundle\Entity\Source\Source as SourceDefinition;
use Spipu\DashboardBundle\Service\Ui\Widget\WidgetRequest;

abstract class AbstractDataProvider implements DataProviderInterface
{
    protected WidgetRequest $request;
    protected SourceDefinition $definition;
    private ?array $filters = null;

    public function getRequest(): WidgetRequest
    {
        return $this->request;
    }

    public function setSourceRequest(WidgetRequest $request): void
    {
        $this->request = $request;
    }

    public function getDefinition(): SourceDefinition
    {
        return $this->definition;
    }

    public function setSourceDefinition(SourceDefinition $definition): void
    {
        $this->definition = $definition;
    }

    public function getFilters(): array
    {
        if (is_array($this->filters)) {
            return $this->filters;
        }

        return $this->request->getFilters();
    }

    protected function getPreviousPeriodDate(): array
    {
        $currentFrom = $this->request->getPeriod()->getDateFrom();
        $currentTo = $this->request->getPeriod()->getDateTo();
        $dateFrom = (clone $currentFrom)->add($currentTo->diff($currentFrom));
        $dateTo = $currentFrom;

        return array($dateFrom, $dateTo);
    }

    public function getSpecificValues(): array
    {
        return [];
    }
}
