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

namespace Spipu\DashboardBundle\Service\Ui\Source\DataProvider;

use Spipu\DashboardBundle\Entity\Source\Source as SourceDefinition;
use Spipu\DashboardBundle\Service\Ui\WidgetRequest;

interface DataProviderInterface
{
    public function setSourceRequest(WidgetRequest $request): void;

    public function setSourceDefinition(SourceDefinition $definition): void;

    public function getValue(): float;

    public function getPreviousValue(): float;

    public function getValues(): array;

    public function getSpecificValues(): array;
}
