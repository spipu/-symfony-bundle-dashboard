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
    /**
     * @var Row
     */
    private Row $row;

    /**
     * @var int
     */
    private int $width;

    /**
     * @var Widget[]
     */
    private array $widgets = [];

    /**
     * @param Row $row
     * @param int $width
     */
    public function __construct(Row $row, int $width)
    {
        $this->row = $row;
        $this->width = $width;
    }

    /**
     * @return Row
     */
    public function getRow(): Row
    {
        return $this->row;
    }

    /**
     * @return int
     */
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

    /**
     * @param Widget $widget
     * @return $this
     */
    public function addWidget(Widget $widget): self
    {
        $this->widgets[] = $widget;

        return $this;
    }
}
