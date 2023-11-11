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
    private bool $canSelect = true;
    private bool $canCreate = true;
    private bool $canConfigure = true;
    private bool $canDelete = true;
    private ?UserInterface $defaultUser = null;

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

    public function setDefaultUser(?UserInterface $defaultUser): DashboardAcl
    {
        $this->defaultUser = $defaultUser;
        return $this;
    }

    public function isCanSelect(): bool
    {
        return $this->canSelect;
    }

    public function isCanCreate(): bool
    {
        return $this->canCreate;
    }

    public function isCanConfigure(): bool
    {
        return $this->canConfigure;
    }

    public function isCanDelete(): bool
    {
        return $this->canDelete;
    }

    public function getDefaultUser(): ?UserInterface
    {
        return $this->defaultUser;
    }
}
