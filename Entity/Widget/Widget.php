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

namespace Spipu\DashboardBundle\Entity\Widget;

use Spipu\DashboardBundle\Entity\Period;
use Spipu\DashboardBundle\Entity\Source\Source as SourceDefinition;

class Widget
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $sourceLabel;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var Period
     */
    private Period $period;

    /**
     * @var array
     */
    private array $values = [];

    /**
     * @var int
     */
    private int $height = 0;

    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @var string[]
     */
    private array $templates = [
        'all'    => '@SpipuDashboard/widget/all.html.twig',
        'header' => '@SpipuDashboard/widget/header.html.twig',
        'config' => '@SpipuDashboard/widget/config.html.twig',
    ];

    /**
     * @var SourceDefinition|null
     */
    private ?SourceDefinition $source = null;

    /**
     * @var int
     */
    private int $generationTime = 0;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return SourceDefinition|null
     */
    public function getSource(): ?SourceDefinition
    {
        return $this->source;
    }

    /**
     * @param SourceDefinition $source
     * @return Widget
     */
    public function setSource(SourceDefinition $source): Widget
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceLabel(): string
    {
        return $this->sourceLabel;
    }

    /**
     * @param string $sourceLabel
     * @return Widget
     */
    public function setSourceLabel(string $sourceLabel): Widget
    {
        $this->sourceLabel = $sourceLabel;

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
     * @return Widget
     */
    public function setType(string $type): Widget
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Period
     */
    public function getPeriod(): Period
    {
        return $this->period;
    }

    /**
     * @param Period $period
     * @return Widget
     */
    public function setPeriod(Period $period): Widget
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     * @return Widget
     */
    public function setValues(array $values): Widget
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return array
     */
    public function getDefinition(): array
    {
        return [
            'code' => $this->source->getCode(),
            'label' => $this->sourceLabel,
            'type' => $this->type,
            'height' => $this->height,
            'period' => [
                'type' => $this->period->getType(),
                'from' => $this->period->getDateFrom()->format('Y-m-d H:i:s'),
                'to' => $this->period->getDateTo()->format('Y-m-d H:i:s'),
                'step' => $this->period->getStep(),
            ],
            'values' => $this->values,
        ];
    }

    /**
     * @param int $height
     * @return Widget
     */
    public function setHeight(int $height): Widget
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     * @return Widget
     */
    public function setFilters(array $filters): Widget
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateAll(): string
    {
        return $this->templates['all'];
    }

    /**
     * @param string $templateAll
     * @return self
     */
    public function setTemplateAll(string $templateAll): self
    {
        $this->templates['all'] = $templateAll;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateHeader(): string
    {
        return $this->templates['header'];
    }

    /**
     * @param string $templateHeader
     * @return self
     */
    public function setTemplateHeader(string $templateHeader): self
    {
        $this->templates['header'] = $templateHeader;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateConfig(): string
    {
        return $this->templates['config'];
    }

    /**
     * @param string $templateConfig
     * @return self
     */
    public function setTemplateConfig(string $templateConfig): self
    {
        $this->templates['config'] = $templateConfig;

        return $this;
    }

    /**
     * @return int
     */
    public function getGenerationTime(): int
    {
        return $this->generationTime;
    }

    /**
     * @param int $generationTime
     * @return Widget
     */
    public function setGenerationTime(int $generationTime): self
    {
        $this->generationTime = $generationTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTooSlow(): bool
    {
        return ($this->generationTime > 150000);
    }
}
