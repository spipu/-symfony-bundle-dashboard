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

namespace Spipu\DashboardBundle\Entity;

use DateTimeInterface;

class Period
{
    /**
     * @var string
     */
    private string $type;

    /**
     * @var DateTimeInterface
     */
    private DateTimeInterface $dateFrom;

    /**
     * @var DateTimeInterface
     */
    private DateTimeInterface $dateTo;

    /**
     * @var int
     */
    private int $step;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Period
     */
    public function setType(string $type): Period
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateFrom(): DateTimeInterface
    {
        return $this->dateFrom;
    }

    /**
     * @param DateTimeInterface $dateFrom
     * @return Period
     */
    public function setDateFrom(DateTimeInterface $dateFrom): Period
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTo(): DateTimeInterface
    {
        return $this->dateTo;
    }

    /**
     * @param DateTimeInterface $dateTo
     * @return Period
     */
    public function setDateTo(DateTimeInterface $dateTo): Period
    {
        $this->dateTo = $dateTo;
        return $this;
    }

    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->step;
    }

    /**
     * @param int $step
     * @return Period
     */
    public function setStep(int $step): Period
    {
        $this->step = $step;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateToReal(): DateTimeInterface
    {
        $date = clone $this->dateTo;
        $date->modify("-{$this->step} second");

        return $date;
    }
}
