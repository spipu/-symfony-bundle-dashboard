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

use Spipu\DashboardBundle\Entity\Source\Source;
use Spipu\DashboardBundle\Exception\SourceException;
use Spipu\DashboardBundle\Source\SourceDefinitionInterface;

class SourceManager
{
    /**
     * @param Source $source
     * @param float $value
     * @return string
     * @throws SourceException
     */
    public function convertValue(Source $source, float $value): string
    {
        switch ($source->getType()) {
            case SourceDefinitionInterface::TYPE_FLOAT:
                return number_format($value, 2, '.', '');

            case SourceDefinitionInterface::TYPE_INT:
                return (string) round($value);
        }

        throw new SourceException('unknown source type');
    }
}
