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

class Screen
{
    /**
     * @var Row[]
     */
    private array $rows = [];

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param string $title
     * @param int $nbCols
     * @return Row
     */
    public function addRow(string $title, int $nbCols): Row
    {
        $row = new Row($this, count($this->rows) + 1, $title, $nbCols);
        $this->rows[] = $row;

        return $row;
    }

    /**
     * @return Widget[]
     */
    public function getWidgets(): array
    {
        $widgets = [];
        foreach ($this->getRows() as $row) {
            foreach ($row->getCols() as $col) {
                foreach ($col->getWidgets() as $widget) {
                    $widgets[] = $widget;
                }
            }
        }

        return $widgets;
    }
}
