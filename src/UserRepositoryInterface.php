<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace Zend\Expressive\Authentication;

interface UserRepositoryInterface
{
    /**
     * Authenticate the credential (username) using a password
     * or using only the credential string (e.g. token based credential)
     * It returns the authenticated user or null.
     *
     * @param string $credential can be also a token
     * @param string $password
     * @return UserInterface|null
     */
    public function authenticate(string $credential, string $password = null): ?UserInterface;
}
