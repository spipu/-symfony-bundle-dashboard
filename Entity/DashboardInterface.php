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

namespace Spipu\DashboardBundle\Entity;

interface DashboardInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * @return string
     */
    public function getUserIdentifier(): string;

    /**
     * @param string $userIdentifier
     * @return DashboardConfig
     */
    public function setUserIdentifier(string $userIdentifier): DashboardConfig;

    /**
     * @return array
     */
    public function getContent(): array;

    /**
     * @param array|null $content
     * @return $this
     */
    public function setContent(?array $content): self;
}
