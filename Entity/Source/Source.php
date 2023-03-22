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

namespace Spipu\DashboardBundle\Entity\Source;

use Spipu\DashboardBundle\Source\SourceDefinitionInterface;

abstract class Source
{
    /**
     * @var string
     */
    private string $code;

    /**
     * @var string|null
     */
    private ?string $entityName;

    /**
     * @var string
     */
    private string $dataProviderServiceName;

    /**
     * @var string
     */
    private string $type = SourceDefinitionInterface::TYPE_INT;

    /**
     * @var string
     */
    private string $suffix = '';

    /**
     * @var string|null
     */
    private ?string $dateField;

    /**
     * @var string
     */
    private string $valueExpression;

    /**
     * @var bool
     */
    private bool $lowerBetter = false;

    /**
     * @var string[]
     */
    private array $conditions = [];

    /**
     * @var SourceFilter[]
     */
    private array $filters = [];

    /**
     * @var string|null
     */
    private ?string $specificDisplayIcon = null;

    /**
     * @var string|null
     */
    private ?string $specificDisplayTemplate = null;

    /**
     * @param string $code
     * @param string|null $entityName
     */
    public function __construct(string $code, ?string $entityName = null)
    {
        $this->code = $code;
        $this->entityName = $entityName;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getEntityName(): ?string
    {
        return $this->entityName;
    }

    /**
     * @param string|null $entityName
     * @return self
     */
    public function setEntityName(?string $entityName): self
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataProviderServiceName(): string
    {
        return $this->dataProviderServiceName;
    }

    /**
     * @param string $dataProviderServiceName
     * @return $this
     */
    protected function setDataProviderServiceName(string $dataProviderServiceName): self
    {
        $this->dataProviderServiceName = $dataProviderServiceName;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function setSuffix(string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDateField(): ?string
    {
        return $this->dateField;
    }

    /**
     * @param string|null $dateField
     * @return $this
     */
    public function setDateField(?string $dateField): self
    {
        $this->dateField = $dateField;

        return $this;
    }

    /**
     * @return string
     */
    public function getValueExpression(): string
    {
        return $this->valueExpression;
    }

    /**
     * @param string $valueExpression
     * @return $this
     */
    public function setValueExpression(string $valueExpression): self
    {
        $this->valueExpression = $valueExpression;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLowerBetter(): bool
    {
        return $this->lowerBetter;
    }

    /**
     * @param bool $lowerBetter
     * @return $this
     */
    public function setLowerBetter(bool $lowerBetter): self
    {
        $this->lowerBetter = $lowerBetter;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param array $conditions
     * @return $this
     */
    public function setConditions(array $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @param string $condition
     * @return $this
     */
    public function addCondition(string $condition): self
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * @return SourceFilter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param SourceFilter $filter
     * @return $this
     */
    public function addFilter(SourceFilter $filter): self
    {
        $this->filters[$filter->getCode()] = $filter;

        return $this;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function removeFilter(string $code): self
    {
        if (array_key_exists($code, $this->filters)) {
            unset($this->filters[$code]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasFilters(): bool
    {
        return !empty($this->filters);
    }

    /**
     * @param string $code
     * @return SourceFilter|null
     */
    public function getFilter(string $code): ?SourceFilter
    {
        return $this->filters[$code] ?? null;
    }

    /**
     * @param string $icon
     * @param string $template
     * @return $this
     */
    public function setSpecificDisplay(string $icon, string $template): self
    {
        $this->specificDisplayIcon = $icon;
        $this->specificDisplayTemplate = $template;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSpecificDisplay(): bool
    {
        return $this->specificDisplayIcon !== null;
    }

    /**
     * @return string|null
     */
    public function getSpecificDisplayIcon(): ?string
    {
        return $this->specificDisplayIcon;
    }

    /**
     * @return string|null
     */
    public function getSpecificDisplayTemplate(): ?string
    {
        return $this->specificDisplayTemplate;
    }

    /**
     * @return bool
     */
    public function needPeriod(): bool
    {
        return (!$this->hasSpecificDisplay()) && ($this->getDateField() !== null);
    }
}
