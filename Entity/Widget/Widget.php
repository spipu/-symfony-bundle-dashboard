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
    private string $id;
    private string $sourceLabel;
    private string $type;
    private Period $period;
    private array $values = [];
    private int $height = 0;
    private array $filters = [];
    private ?SourceDefinition $source = null;
    private int $generationTime = 0;
    private array $templates = [
        'all'    => '@SpipuDashboard/widget/all.html.twig',
        'header' => '@SpipuDashboard/widget/header.html.twig',
        'config' => '@SpipuDashboard/widget/config.html.twig',
    ];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSource(): ?SourceDefinition
    {
        return $this->source;
    }

    public function setSource(SourceDefinition $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getSourceLabel(): string
    {
        return $this->sourceLabel;
    }

    public function setSourceLabel(string $sourceLabel): self
    {
        $this->sourceLabel = $sourceLabel;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPeriod(): Period
    {
        return $this->period;
    }

    public function setPeriod(Period $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }

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

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function getTemplateAll(): string
    {
        return $this->templates['all'];
    }

    public function setTemplateAll(string $templateAll): self
    {
        $this->templates['all'] = $templateAll;

        return $this;
    }

    public function getTemplateHeader(): string
    {
        return $this->templates['header'];
    }

    public function setTemplateHeader(string $templateHeader): self
    {
        $this->templates['header'] = $templateHeader;

        return $this;
    }

    public function getTemplateConfig(): string
    {
        return $this->templates['config'];
    }

    public function setTemplateConfig(string $templateConfig): self
    {
        $this->templates['config'] = $templateConfig;

        return $this;
    }

    public function getGenerationTime(): int
    {
        return $this->generationTime;
    }

    public function setGenerationTime(int $generationTime): self
    {
        $this->generationTime = $generationTime;

        return $this;
    }

    public function isTooSlow(): bool
    {
        return ($this->generationTime > 200000);
    }
}
