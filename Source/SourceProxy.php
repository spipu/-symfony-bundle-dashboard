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
    /**
     * @var SourceDefinitionInterface
     */
    private SourceDefinitionInterface $source;

    /**
     * @var SourceDefinition|null
     */
    private ?SourceDefinition $sourceDefinitionCache = null;

    /**
     * @param SourceDefinitionInterface $source
     * @return $this
     */
    public function setSource(SourceDefinitionInterface $source): self
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return SourceDefinition
     */
    public function getDefinition(): SourceDefinition
    {
        if ($this->sourceDefinitionCache === null) {
            $this->sourceDefinitionCache = $this->source->getDefinition();
        }

        return $this->sourceDefinitionCache;
    }

    /**
     * @return array|string[]
     */
    public function getRolesNeeded(): array
    {
        return $this->source->getRolesNeeded();
    }
}
