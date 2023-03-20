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

namespace Spipu\DashboardBundle\Service;

use DateTime;
use Exception;
use Spipu\DashboardBundle\Entity\Period;
use Spipu\DashboardBundle\Exception\PeriodException;
use Symfony\Contracts\Translation\TranslatorInterface;

class PeriodService
{
    public const PERIOD_HOUR = 'hour';
    public const PERIOD_DAY_CURRENT = 'day-current';
    public const PERIOD_DAY_FULL = 'day-full';
    public const PERIOD_WEEK = 'week';
    public const PERIOD_MONTH = 'month';
    public const PERIOD_YEAR = 'year';

    /**
     * @var string[]
     */
    protected array $types = [
        self::PERIOD_HOUR,
        self::PERIOD_DAY_CURRENT,
        self::PERIOD_DAY_FULL,
        self::PERIOD_WEEK,
        self::PERIOD_MONTH,
        self::PERIOD_YEAR,
    ];

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return string[]
     */
    public function getDefinitions(): array
    {
        $definition = [];

        foreach ($this->types as $type) {
            $definition[$type] = [
                'code' => $type,
                'label' => $this->translator->trans('spipu.dashboard.period.' . $type),
            ];
        }

        return $definition;
    }

    /**
     * @param string $type
     * @param DateTime|null $dateFrom
     * @param DateTime|null $dateTo
     * @return Period
     * @throws PeriodException
     */
    public function create(string $type, ?DateTime $dateFrom = null, ?DateTime $dateTo = null): Period
    {
        $period = new Period();
        $period->setType($type);

        try {
            switch ($type) {
                case 'year':
                    return $this->preparePeriodYear($period);

                case 'month':
                    return $this->preparePeriodMonth($period);

                case 'week':
                    return $this->preparePeriodWeek($period);

                case 'day-full':
                    return $this->preparePeriodDayFull($period);

                case 'day-current':
                    return $this->preparePeriodDayCurrent($period);

                case 'hour':
                    return $this->preparePeriodHour($period);

                case 'custom':
                    return $this->preparePeriodCustom($period, $dateFrom, $dateTo);
            }
        } catch (Exception $e) {
            throw new PeriodException($e->getMessage());
        }

        throw new PeriodException('unknown period type code');
    }

    /**
     * @param Period $period
     * @return Period
     * @throws Exception
     */
    private function preparePeriodYear(Period $period): Period
    {
        $time = time();

        $period
            ->setDateFrom((new DateTime(date('Y-m-d 00:00:00', $time)))->modify("-1 year"))
            ->setDateTo((new DateTime(date('Y-m-d 00:00:00', $time))))
            ->setStep(3600 * 24 * 365 / 24);

        return $period;
    }

    /**
     * @param Period $period
     * @return Period
     * @throws Exception
     */
    private function preparePeriodMonth(Period $period): Period
    {
        $time = time();

        $period
            ->setDateFrom((new DateTime(date('Y-m-d 00:00:00', $time)))->modify("-1 month"))
            ->setDateTo((new DateTime(date('Y-m-d 00:00:00', $time))))
            ->setStep(3600 * 24);

        return $period;
    }

    /**
     * @param Period $period
     * @return Period
     * @throws Exception
     */
    private function preparePeriodWeek(Period $period): Period
    {
        $time = time();

        $period
            ->setDateFrom((new DateTime(date('Y-m-d 00:00:00', $time)))->modify("-1 week"))
            ->setDateTo((new DateTime(date('Y-m-d 00:00:00', $time))))
            ->setStep(3600 * 24);

        return $period;
    }

    /**
     * @param Period $period
     * @return Period
     * @throws Exception
     */
    private function preparePeriodDayFull(Period $period): Period
    {
        $time = time();

        $period
            ->setDateFrom((new DateTime(date('Y-m-d 00:00:00', $time)))->modify("-1 day"))
            ->setDateTo((new DateTime(date('Y-m-d 00:00:00', $time))))
            ->setStep(3600);

        return $period;
    }

    /**
     * @param Period $period
     * @return Period
     * @throws Exception
     */
    private function preparePeriodDayCurrent(Period $period): Period
    {
        $time = time();

        $period
            ->setDateFrom(new DateTime(date('Y-m-d 00:00:00', $time)))
            ->setDateTo((new DateTime(date('Y-m-d H:00:00', $time)))->modify("+1 hour"))
            ->setStep(3600);

        return $period;
    }

    /**
     * @param Period $period
     * @return Period
     * @throws Exception
     */
    private function preparePeriodHour(Period $period): Period
    {
        $time = time();

        $period
            ->setDateFrom((new DateTime(date('Y-m-d H:i:00', $time)))->modify("-1 hour"))
            ->setDateTo(new DateTime(date('Y-m-d H:i:00', $time)))
            ->setStep(60);

        return $period;
    }

    /**
     * @param Period $period
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @return Period
     */
    private function preparePeriodCustom(Period $period, DateTime $dateFrom, DateTime $dateTo): Period
    {
        if ($dateFrom > $dateTo) {
            $tempDateFrom = $dateFrom;
            $dateFrom = $dateTo;
            $dateTo = $tempDateFrom;
        }
        $interval = $dateTo->getTimestamp() - $dateFrom->getTimestamp();

        // Default : 1 month.
        $step = 3600 * 24 * 365 / 12;

        // 6 months => 1 week.
        if ($interval < (3600 * 24 * 365 / 2)) {
            $step = 3600 * 24 * 7;
        }

        // 1 month => 1 day.
        if ($interval < (3600 * 24 * 365 / 12)) {
            $step = 3600 * 24;
        }

        // 72 hours => 2 hours.
        if ($interval < 72 * 3600) {
            $step = 3600 * 2;
        }

        // 24 hours => 60 minutes.
        if ($interval < 24 * 3600) {
            $step = 3600;
        }

        // 2 hour => 15 minutes.
        if ($interval < 3600 * 2) {
            $step = 60 * 15;
        }

        // 1 hour => 1 minute.
        if ($interval < 3600) {
            $step = 60;
        }

        $period->setDateFrom($dateFrom)
            ->setDateTo($dateTo)
            ->setStep($step);

        return $period;
    }
}
