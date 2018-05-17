<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

class DefaultUser implements UserInterface
{
    protected $identity;

    protected $roles = [];

    public function setIdentity(string $identity) : void
    {
        $this->identity = $identity;
    }

    public function getIdentity() : string
    {
        return $this->identity;
    }

    public function setRoles(array $roles) : void
    {
        $this->roles = $roles;
    }

    public function getRoles() : array
    {
        return $this->roles;
    }
}
