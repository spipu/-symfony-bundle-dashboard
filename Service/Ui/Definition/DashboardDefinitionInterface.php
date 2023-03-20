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

namespace Spipu\DashboardBundle\Service\Ui\Definition;

use Spipu\DashboardBundle\Entity\Dashboard\Dashboard as DashboardDefinition;

interface DashboardDefinitionInterface
{
    /**
     * @return DashboardDefinition
     */
    public function getDefinition(): DashboardDefinition;

    /**
     * @return array
     */
    public function getDefaultConfig(): array;
}
