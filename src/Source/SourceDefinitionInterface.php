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

use Spipu\DashboardBundle\Entity\Source\Source as SourceDefinition;

interface SourceDefinitionInterface
{
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';

    public function getDefinition(): SourceDefinition;

    public function getRolesNeeded(): array;
}
