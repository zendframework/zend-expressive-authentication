<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

interface UserInterface
{
    /**
     * Set the user's identity
     */
    public function setIdentity(string $identity) : void;

    /**
     * Get the unique user identity (id, username, email address or ...)
     */
    public function getIdentity() : string;

    /**
     * Set the user's roles
     */
    public function setRoles(array $roles) : void;

    /**
     * Get all user roles
     *
     * @return string[]
     */
    public function getRoles() : array;
}
