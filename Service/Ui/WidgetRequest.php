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

use Spipu\DashboardBundle\Entity\Period;
use Spipu\DashboardBundle\Entity\Widget\Widget;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class WidgetRequest extends AbstractRequest
{
    public const KEY_FILTERS = 'fl';

    /**
     * @var Widget
     */
    private Widget $definition;

    /**
     * @var Period|null
     */
    private ?Period $period = null;
    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @param SymfonyRequest $request
     * @param Widget $definition
     */
    public function __construct(
        SymfonyRequest $request,
        Widget $definition
    ) {
        parent::__construct($request);
        $this->definition = $definition;
    }

    /**
     * @return void
     */
    public function prepare(): void
    {
        $this->setSessionPrefixKey('widget.' . $this->definition->getId());
        $this->prepareFilters();
    }

    /**
     * @return void
     * @SuppressWarnings(PMD.CyclomaticComplexity)
     */
    private function prepareFilters(): void
    {
        $this->filters = [];
        $this->filters = $this->getSessionValue('filters', $this->filters);
        $this->filters = (array)$this->request->get(self::KEY_FILTERS, $this->filters);
        if ($this->request->get(self::KEY_FILTERS) === null) {
            $this->filters = $this->definition->getFilters();
        }
        foreach ($this->filters as $key => $value) {
            $filter = $this->definition->getSource()->getFilter($key);
            if (!$filter) {
                unset($this->filters[$key]);
                continue;
            }

            if ($value === null) {
                unset($this->filters[$key]);
                continue;
            }

            if (is_array($value)) {
                if (!array_filter($value)) {
                    unset($this->filters[$key]);
                    continue;
                }
                $this->filters[$key] = $value;
                continue;
            }
            $this->filters[$key] = trim((string)$value);
            if ($this->filters[$key] === '') {
                unset($this->filters[$key]);
            }
        }

        $this->setSessionValue('filters', $this->filters);
    }

    /**
     * @return string[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param string $key
     * @param string|null $subKey
     * @return string
     */
    public function getFilterValueString(string $key, string $subKey = null): string
    {
        if (!array_key_exists($key, $this->filters)) {
            return '';
        }

        if ($subKey === null) {
            return $this->filters[$key];
        }

        if (!is_array($this->filters[$key])) {
            return '';
        }

        if (!array_key_exists($subKey, $this->filters[$key])) {
            return '';
        }

        return $this->filters[$key][$subKey];
    }


    /**
     * @param string $key
     * @param string|null $subKey
     * @return array
     */
    public function getFilterValueArray(string $key, string $subKey = null): array
    {
        if (!array_key_exists($key, $this->filters)) {
            return [];
        }

        if ($subKey === null) {
            return $this->filters[$key];
        }

        if (!is_array($this->filters[$key])) {
            return [];
        }

        if (!array_key_exists($subKey, $this->filters[$key])) {
            return [];
        }

        return $this->filters[$key][$subKey];
    }

    /**
     * @return Period|null
     */
    public function getPeriod(): ?Period
    {
        if (!$this->period) {
            return $this->definition->getPeriod();
        }

        return $this->period;
    }

    /**
     * @param Period|null $period
     * @return WidgetRequest
     */
    public function setPeriod(?Period $period): WidgetRequest
    {
        $this->period = $period;

        return $this;
    }
}
