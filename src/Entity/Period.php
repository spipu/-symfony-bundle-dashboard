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
    private string $type;
    private DateTimeInterface $dateFrom;
    private DateTimeInterface $dateTo;
    private int $step;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDateFrom(): DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(DateTimeInterface $dateFrom): self
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    public function getDateTo(): DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(DateTimeInterface $dateTo): self
    {
        $this->dateTo = $dateTo;
        return $this;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function setStep(int $step): self
    {
        $this->step = $step;
        return $this;
    }

    public function getDateToReal(): DateTimeInterface
    {
        $date = clone $this->dateTo;
        $date->modify("-{$this->step} second");

        return $date;
    }
}
