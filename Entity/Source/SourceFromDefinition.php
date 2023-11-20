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

namespace Spipu\DashboardBundle\Entity\Source;

use Spipu\DashboardBundle\Source\SourceDataDefinitionInterface;

class SourceFromDefinition extends Source
{
    /**
     * @var SourceDataDefinitionInterface
     */
    private SourceDataDefinitionInterface $sourceDefinition;

    /**
     * @param string $code
     * @param SourceDataDefinitionInterface $sourceDefinition
     */
    public function __construct(
        string $code,
        SourceDataDefinitionInterface $sourceDefinition
    ) {
        parent::__construct($code, null);

        $this->sourceDefinition = $sourceDefinition;

        $this
            ->setDataProviderServiceName('Spipu\DashboardBundle\Service\Ui\Source\DataProvider\FromSourceDefinition')
            ->setValueExpression('')
            ->setDateField(null)
        ;
    }

    /**
     * @return SourceDataDefinitionInterface
     */
    public function getSourceDefinition(): SourceDataDefinitionInterface
    {
        return $this->sourceDefinition;
    }
}
