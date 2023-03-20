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

class SourceDql extends Source
{
    /**
     * @param string $code
     * @param string|null $entityName
     */
    public function __construct(string $code, ?string $entityName = null)
    {
        parent::__construct($code, $entityName);

        $this
            ->setDataProviderServiceName('\Spipu\DashboardBundle\Service\Ui\Source\DataProvider\DoctrineDql')
            ->setValueExpression('COUNT(main)')
            ->setDateField('createdAt')
        ;
    }
}
