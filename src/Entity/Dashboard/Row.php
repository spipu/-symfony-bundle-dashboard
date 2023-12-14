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

class Row
{
    private Screen $screen;
    private int $id;
    private string $title;
    private int $nbCols;

    /**
     * @var Column[]
     */
    private array $cols = [];

    public function __construct(Screen $screen, int $id, string $title, int $nbCols)
    {
        $this->id = $id;
        $this->screen = $screen;
        $this->title = $title;
        $this->nbCols = $nbCols;
    }

    public function getScreen(): Screen
    {
        return $this->screen;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Column[]
     */
    public function getCols(): array
    {
        return $this->cols;
    }

    public function addCol(int $width): Column
    {
        $col = new Column($this, count($this->cols) + 1, $width);

        $this->cols[] = $col;

        return $col;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getNbCols(): int
    {
        return $this->nbCols;
    }
}
