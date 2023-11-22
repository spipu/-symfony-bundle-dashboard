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
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Throwable;

class DashboardRequest
{
    public const KEY_PERIOD = 'dp';

    /**
     * @var SymfonyRequest
     */
    private SymfonyRequest $request;

    /**
     * @var PeriodService
     */
    private PeriodService $periodService;

    /**
     * @var DashboardConfig
     */
    private DashboardConfig $definition;

    /**
     * @var string
     */
    private string $sessionPrefixKey;

    /**
     * @var Period|null
     */
    private ?Period $period = null;

    /**
     * @param SymfonyRequest $request
     * @param PeriodService $periodService
     * @param DashboardConfig $definition
     */
    public function __construct(
        SymfonyRequest $request,
        PeriodService $periodService,
        DashboardConfig $definition
    ) {
        $this->request = $request;
        $this->periodService = $periodService;
        $this->definition = $definition;
    }

    /**
     * @return void
     */
    public function prepare()
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
            $requestPeriod = (array) $this->request->get(self::KEY_PERIOD, []);
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

            $type = $requestPeriod['type'] !== '' ? $requestPeriod['type'] : PeriodService::PERIOD_CUSTOM;
            $dateFrom = $requestPeriod['from'] ?? null;
            $dateTo = $requestPeriod['to'] ?? null;
            if ($type !== PeriodService::PERIOD_CUSTOM || ($dateFrom && $dateTo)) {
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

    /**
     * @return Period|null
     */
    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getSessionKey(string $key): string
    {
        return $this->sessionPrefixKey . '.' . $key;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSessionValue(string $key, $default)
    {
        return $this->request->getSession()->get($this->getSessionKey($key), $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private function setSessionValue(string $key, $value): void
    {
        $this->request->getSession()->set($this->getSessionKey($key), $value);
    }

    /**
     * @param string $sessionPrefixKey
     * @return void
     */
    private function setSessionPrefixKey(string $sessionPrefixKey): void
    {
        $this->sessionPrefixKey = $sessionPrefixKey;
    }
}
