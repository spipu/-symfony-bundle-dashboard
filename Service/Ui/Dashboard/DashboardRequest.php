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

namespace Spipu\DashboardBundle\Service\Ui\Dashboard;

use DateTime;
use Spipu\DashboardBundle\Entity\DashboardConfig;
use Spipu\DashboardBundle\Entity\Period;
use Spipu\DashboardBundle\Service\PeriodService;
use Spipu\DashboardBundle\Service\Ui\AbstractRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

class DashboardRequest extends AbstractRequest
{
    public const KEY_PERIOD = 'dp';

    private PeriodService $periodService;
    private DashboardConfig $definition;
    private ?Period $period = null;

    public function __construct(
        RequestStack $requestStack,
        PeriodService $periodService,
        DashboardConfig $definition
    ) {
        parent::__construct($requestStack);
        $this->periodService = $periodService;
        $this->definition = $definition;
    }

    public function prepare(): void
    {
        $this->setSessionPrefixKey('dashboard.' . $this->definition->getId());
        $this->preparePeriod();
    }

    /**
     * @return void
     * @SuppressWarnings(PMD.CyclomaticComplexity)
     */
    private function preparePeriod(): void
    {
        try {
            $this->period = null;
            $this->period = $this->getSessionValue('period', $this->period);
            $requestPeriod = (array) $this->getCurrentRequest()->get(self::KEY_PERIOD, []);

            // Keep session.
            if (empty($requestPeriod)) {
                return;
            }

            // Reset period.
            if (empty($requestPeriod['type']) && empty($requestPeriod['from']) && empty($requestPeriod['to'])) {
                $this->period = null;
                $this->setSessionValue('period', $this->period);
                return;
            }

            $type = $requestPeriod['type'] !== '' ? $requestPeriod['type'] : 'custom';
            $dateFrom = $requestPeriod['from'] ?? null;
            $dateTo = $requestPeriod['to'] ?? null;
            if ($type !== 'custom' || ($dateFrom && $dateTo)) {
                $this->period = $this->periodService->create(
                    $type,
                    $dateFrom ? new DateTime($dateFrom) : null,
                    $dateTo ? new DateTime($dateTo) : null
                );
            }
        } catch (Throwable $throwable) {
            $this->period = null;
        }
        $this->setSessionValue('period', $this->period);
    }

    public function getPeriod(): ?Period
    {
        return $this->period;
    }
}
