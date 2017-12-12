<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace Zend\Expressive\Authentication;

interface UserInterface
{
    /**
     * Get the username
     */
    public function getUsername() : string;

    /**
     * Get all user roles
     *
     * @return string[]
     */
    public function getUserRoles() : array;
}
