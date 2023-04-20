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
    /**
     * @var Screen
     */
    private Screen $screen;

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var int
     */
    private int $nbCols;

    /**
     * @var Column[]
     */
    private array $cols = [];

    /**
     * @param Screen $screen
     * @param int $id
     * @param string $title
     * @param int $nbCols
     */
    public function __construct(Screen $screen, int $id, string $title, int $nbCols)
    {
        $this->id = $id;
        $this->screen = $screen;
        $this->title = $title;
        $this->nbCols = $nbCols;
    }

    /**
     * @return Screen
     */
    public function getScreen(): Screen
    {
        return $this->screen;
    }

    /**
     * @return int
     */
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

    /**
     * @param int $width
     * @return Column
     */
    public function addCol(int $width): Column
    {
        $col = new Column($this, count($this->cols) + 1, $width);

        $this->cols[] = $col;

        return $col;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getNbCols(): int
    {
        return $this->nbCols;
    }
}
