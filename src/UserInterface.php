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
     * Create user from (session) state
     */
    public static function fromState(array $data) : UserInterface;

    /**
     * Extract user data for (session) storage
     */
    public function toState() : array;

    /**
     * Get the unique user identity (id, username, email address or ...)
     */
    public function getIdentity() : string;

    /**
     * Get all user roles
     *
     * @return string[]
     */
    public function getUserRoles() : array;
}
