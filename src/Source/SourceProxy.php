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

class SourceProxy implements SourceDefinitionInterface
{
    private SourceDefinitionInterface $source;
    private ?SourceDefinition $sourceDefinitionCache = null;

    public function setSource(SourceDefinitionInterface $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getDefinition(): SourceDefinition
    {
        if ($this->sourceDefinitionCache === null) {
            $this->sourceDefinitionCache = $this->source->getDefinition();
        }

        return $this->sourceDefinitionCache;
    }

    public function getRolesNeeded(): array
    {
        return $this->source->getRolesNeeded();
    }
}
