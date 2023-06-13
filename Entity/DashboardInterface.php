<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spipu\DashboardBundle\Entity;

interface DashboardInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(string $name): self;

    public function getUserIdentifier(): string;

    public function setUserIdentifier(string $userIdentifier): DashboardConfig;

    public function getContent(): array;

    public function setContent(?array $content): self;
}
