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

class DashboardRouter
{
    /**
     * @var string[]
     */
    private array $urls = [
        'main'           => '',
        'reset'          => '',
        'creation'       => '',
        'duplication'    => '',
        'deletion'       => '',
        'configuration'  => '',
        'refresh_widget' => '',
    ];

    /**
     * @param string $code
     * @param string $url
     * @return $this
     */
    public function setUrl(string $code, string $url): self
    {
        $this->urls[$code] = $url;

        return $this;
    }

    /**
     * @param string $code
     * @return string|null
     */
    public function getUrl(string $code): ?string
    {
        return $this->urls[$code] ?? null;
    }

    /**
     * @return string|null
     */
    public function getResetUrl(): ?string
    {
        if (!$this->getUrl('reset')) {
            return null;
        }

        $parameters = ['dp' => ['type' => '', 'from' => '', 'to' => '']];

        return $this->getUrl('reset') . '?' . http_build_query($parameters);
    }
}
