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

namespace Spipu\DashboardBundle\Service\Ui;

use Spipu\DashboardBundle\Entity\DashboardInterface;
use Spipu\DashboardBundle\Service\PeriodService;
use Spipu\DashboardBundle\Service\Ui\Dashboard\DashboardRequest;
use Symfony\Component\HttpFoundation\RequestStack;

class DashboardRequestFactory
{
    private PeriodService $periodService;
    private RequestStack $requestStack;

    /**
     * @var DashboardRequest[]
     */
    private array $dashboardRequests = [];

    public function __construct(
        PeriodService $periodService,
        RequestStack $requestStack
    ) {
        $this->periodService = $periodService;
        $this->requestStack = $requestStack;
    }

    public function get(DashboardInterface $resource): DashboardRequest
    {
        $dashboardId = $resource->getId();

        if (!array_key_exists($dashboardId, $this->dashboardRequests)) {
            $request = new DashboardRequest(
                $this->requestStack,
                $this->periodService,
                $dashboardId
            );

            $request->prepare();

            $this->dashboardRequests[$dashboardId] = $request;
        }

        return $this->dashboardRequests[$dashboardId];
    }
}
