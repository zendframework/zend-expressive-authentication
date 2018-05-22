<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

final class DefaultUser implements UserInterface
{
    private $identity;

    private $roles;

    private $details;

    public function __construct(string $identity, array $roles = [], array $details = [])
    {
        $this->identity = $identity;
        $this->roles = $roles;
        $this->details = $details;
    }

    public function getIdentity() : string
    {
        return $this->identity;
    }

    public function getRoles() : array
    {
        return $this->roles;
    }

    public function getDetails() : array
    {
        return $this->details;
    }

    public function getDetail(string $name, $default = null)
    {
        return $this->details[$name] ?? $default;
    }
}
