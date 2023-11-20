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

use Spipu\DashboardBundle\Entity\Source\SourceFromDefinition;
use Spipu\DashboardBundle\Entity\Source\Source;
use Spipu\DashboardBundle\Exception\SourceException;

/**
 * @method SourceFromDefinition getDefinition()
 */
class FromSourceDefinition extends AbstractDataProvider
{
    public function setSourceDefinition(Source $definition): void
    {
        if (!($definition instanceof SourceFromDefinition)) {
            throw new SourceException(
                sprintf(
                    'The Source %s is not compatible with this DataProvider',
                    $definition->getCode()
                )
            );
        }

        parent::setSourceDefinition($definition);
    }

    public function getValue(): float
    {
        return $this->getDefinition()->getSourceDefinition()->getValue($this->getRequest());
    }

    public function getPreviousValue(): float
    {
        return $this->getDefinition()->getSourceDefinition()->getPreviousValue($this->getRequest());
    }

    public function getValues(): array
    {
        return $this->getDefinition()->getSourceDefinition()->getValues($this->getRequest());
    }

    public function getSpecificValues(): array
    {
        return $this->getDefinition()->getSourceDefinition()->getSpecificValues($this->getRequest());
    }
}
