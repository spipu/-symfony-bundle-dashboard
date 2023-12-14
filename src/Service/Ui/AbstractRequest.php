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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractRequest
{
    private string $sessionPrefixKey;
    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    protected function getCurrentRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function setSessionPrefixKey(string $sessionPrefixKey): void
    {
        $this->sessionPrefixKey = $sessionPrefixKey;
    }

    protected function getSessionKey(string $key): string
    {
        return $this->sessionPrefixKey . '.' . $key;
    }

    protected function getSessionValue(string $key, mixed $default): mixed
    {
        return $this->requestStack->getSession()->get($this->getSessionKey($key), $default);
    }

    protected function setSessionValue(string $key, mixed $value): void
    {
        $this->requestStack->getSession()->set($this->getSessionKey($key), $value);
    }
}
