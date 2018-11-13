<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

interface UserInterface extends IdentityInterface
{
    /**
     * Get all user roles
     *
     * @return Iterable
     */
    public function getRoles() : iterable;

    /**
     * Get a detail $name if present, $default otherwise
     */
    public function getDetail(string $name, $default = null);

    /**
     * Get all the details, if any
     */
    public function getDetails() : array;
}
