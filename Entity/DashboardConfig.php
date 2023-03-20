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

/**
 * @ORM\Table(
 *     name="spipu_dashboard_config",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="UNIQ_DASHBOARD_CONFIG", columns={"user_identifier", "name"}),
 *     }
 * )
 * @ORM\Entity(repositoryClass=\Spipu\DashboardBundle\Repository\DashboardConfigRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class DashboardConfig implements DashboardInterface
{
    use TimestampableTrait;

    public const DEFAULT_NAME = 'default';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected string $name;

    /**
     * @var array
     * @ORM\Column(type="json", nullable=true)
     */
    protected array $content = [];

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $userIdentifier;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $name = trim(strip_tags($name));

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @param string $userIdentifier
     * @return DashboardConfig
     */
    public function setUserIdentifier(string $userIdentifier): DashboardConfig
    {
        $this->userIdentifier = $userIdentifier;

        return $this;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        $content = $this->content;

        if (!array_key_exists('rows', $content)) {
            $content = ['rows' => []];
        }

        return $content;
    }

    /**
     * @param array|null $content
     * @return $this
     */
    public function setContent(?array $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->name === self::DEFAULT_NAME;
    }
}
