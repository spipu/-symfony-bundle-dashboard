<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spipu\DashboardBundle\Service\Ui;

use Spipu\DashboardBundle\Service\Ui\Source\DataProvider\DataProviderInterface;
use Spipu\UiBundle\Service\Ui\UiManagerInterface;

interface WidgetManagerInterface extends UiManagerInterface
{
    /**
     * @return DataProviderInterface
     */
    public function getDataProvider(): DataProviderInterface;
}
