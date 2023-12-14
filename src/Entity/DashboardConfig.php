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

use Doctrine\ORM\Mapping as ORM;
use Spipu\UiBundle\Entity\TimestampableTrait;

#[ORM\Entity(repositoryClass: 'Spipu\DashboardBundle\Repository\DashboardConfigRepository')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: "spipu_dashboard_config")]
#[ORM\UniqueConstraint(name: "UNIQ_DASHBOARD_CONFIG", columns: ["user_identifier", "name"])]
class DashboardConfig implements DashboardInterface
{
    use TimestampableTrait;

    public const DEFAULT_NAME = 'default';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    protected ?string $name = null;

    #[ORM\Column(type: "json", nullable: true)]
    protected array $content = [];

    #[ORM\Column(length: 255)]
    private ?string $userIdentifier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $name = trim(strip_tags($name));

        $this->name = $name;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier(string $userIdentifier): self
    {
        $this->userIdentifier = $userIdentifier;

        return $this;
    }

    public function getContent(): array
    {
        $content = $this->content;

        if (!array_key_exists('rows', $content)) {
            $content = ['rows' => []];
        }

        return $content;
    }

    public function setContent(?array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->name === self::DEFAULT_NAME;
    }
}
