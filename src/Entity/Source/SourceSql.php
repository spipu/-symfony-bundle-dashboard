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

class SourceSql extends Source
{
    public function __construct(string $code, ?string $entityName = null)
    {
        parent::__construct($code, $entityName);

        $this
            ->setDataProviderServiceName('Spipu\DashboardBundle\Service\Ui\Source\DataProvider\DoctrineSql')
            ->setValueExpression('COUNT(main.id)')
            ->setDateField('created_at')
        ;
    }
}
