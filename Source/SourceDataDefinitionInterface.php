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

namespace Spipu\DashboardBundle\Source;

use Spipu\DashboardBundle\Service\Ui\WidgetRequest;

interface SourceDataDefinitionInterface
{
    public function getValue(WidgetRequest $request): float;

    public function getPreviousValue(WidgetRequest $request): float;

    public function getValues(WidgetRequest $request): array;

    public function getSpecificValues(WidgetRequest $request): array;
}
