<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spipu\DashboardBundle\Service\Ui\Source\DataProvider;

use Spipu\DashboardBundle\Entity\Source\Source as SourceDefinition;
use Spipu\DashboardBundle\Service\Ui\Widget\WidgetRequest;

interface DataProviderInterface
{
    /**
     * @param WidgetRequest $request
     * @return void
     */
    public function setSourceRequest(WidgetRequest $request): void;

    /**
     * @param SourceDefinition $definition
     * @return void
     */
    public function setSourceDefinition(SourceDefinition $definition): void;

    /**
     * @return float
     */
    public function getValue(): float;

    /**
     * @return float
     */
    public function getPreviousValue(): float;

    /**
     * @return array
     */
    public function getValues(): array;
}
