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

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

abstract class AbstractRequest
{
    /**
     * @var string
     */
    private string $sessionPrefixKey;

    /**
     * @var SymfonyRequest
     */
    protected SymfonyRequest $request;

    /**
     * @param SymfonyRequest $request
     */
    public function __construct(
        SymfonyRequest $request
    ) {
        $this->request = $request;
    }
    /**
     * @param string $sessionPrefixKey
     * @return void
     */
    protected function setSessionPrefixKey(string $sessionPrefixKey): void
    {
        $this->sessionPrefixKey = $sessionPrefixKey;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getSessionKey(string $key): string
    {
        return $this->sessionPrefixKey . '.' . $key;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getSessionValue(string $key, $default)
    {
        return $this->request->getSession()->get($this->getSessionKey($key), $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setSessionValue(string $key, $value): void
    {
        $this->request->getSession()->set($this->getSessionKey($key), $value);
    }
}
