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

use Spipu\DashboardBundle\Entity\DashboardAcl;
use Spipu\UiBundle\Service\Ui\UiManagerInterface;

interface DashboardShowManagerInterface extends UiManagerInterface
{
    public function setUrl(string $code, string $url): self;

    public function setAcl(DashboardAcl $dashboardAcl): self;
}
