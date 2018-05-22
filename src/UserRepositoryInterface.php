<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

interface UserRepositoryInterface
{
    /**
     * Authenticate the identity (id, username, email ...) using a password
     * or using only a credential string (e.g. token based credential)
     * It returns the authenticated user or null.
     *
     * @param string $credential can be also a token
     */
    public function authenticate(string $credential, string $password = null) : ?UserInterface;

    /**
     * Get the user roles if present.
     *
     * @param string $identity
     * @return string[]
     */
    public function getUserRoles(string $identity) : array;

    /**
     * Get the user details if present.
     *
     * @param string $identity
     * @return string[]
     */
    public function getUserDetails(string $identity) : array;
}
