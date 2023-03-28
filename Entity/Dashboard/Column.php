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

namespace Spipu\DashboardBundle\Entity\Dashboard;

use Spipu\DashboardBundle\Entity\Widget\Widget;

class Column
{
    private Row $row;
    private int $id;
    private int $width;

    /**
     * @var Widget[]
     */
    private array $widgets = [];

    public function __construct(Row $row, int $id, int $width)
    {
        $this->id = $id;
        $this->row = $row;
        $this->width = $width;
    }

    public function getRow(): Row
    {
        return $this->row;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return Widget[]
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }

    public function addWidget(Widget $widget): self
    {
        $this->widgets[] = $widget;

        return $this;
    }
}
