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

use Spipu\DashboardBundle\Exception\DashboardAclException;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardAcl
{
    /**
     * @var bool
     */
    private bool $canSelect = true;

    /**
     * @var bool
     */
    private bool $canCreate = true;

    /**
     * @var bool
     */
    private bool $canConfigure = true;

    /**
     * @var bool
     */
    private bool $canDelete = true;

    /**
     * @var UserInterface|null
     */
    private ?UserInterface $defaultUser = null;

    /**
     * @param bool $canSelect
     * @param bool $canCreate
     * @param bool $canConfigure
     * @param bool $canDelete
     * @return $this
     * @throws DashboardAclException
     */
    public function configure(bool $canSelect, bool $canCreate, bool $canConfigure, bool $canDelete): DashboardAcl
    {
        if ($canCreate && !$canSelect) {
            throw new DashboardAclException('If you can create, you must be allowed to select');
        }

        if ($canCreate && !$canConfigure) {
            throw new DashboardAclException('If you can create, you must be allowed to configure');
        }

        if ($canDelete && !$canConfigure) {
            throw new DashboardAclException('If you can delete, you must be allowed to configure');
        }

        $this->canSelect = $canSelect;
        $this->canCreate = $canCreate;
        $this->canConfigure = $canConfigure;
        $this->canDelete = $canDelete;

        return $this;
    }

    /**
     * @param UserInterface|null $defaultUser
     * @return $this
     */
    public function setDefaultUser(?UserInterface $defaultUser): DashboardAcl
    {
        $this->defaultUser = $defaultUser;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCanSelect(): bool
    {
        return $this->canSelect;
    }

    /**
     * @return bool
     */
    public function isCanCreate(): bool
    {
        return $this->canCreate;
    }

    /**
     * @return bool
     */
    public function isCanConfigure(): bool
    {
        return $this->canConfigure;
    }

    /**
     * @return bool
     */
    public function isCanDelete(): bool
    {
        return $this->canDelete;
    }

    /**
     * @return UserInterface|null
     */
    public function getDefaultUser(): ?UserInterface
    {
        return $this->defaultUser;
    }
}
