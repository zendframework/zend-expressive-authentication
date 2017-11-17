<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\UserRepository;

use Zend\Expressive\Authentication\UserInterface;

trait UserTrait
{
    /**
     * Generate a user from $username and $role
     */
    protected function generateUser(string $username, string $role) : UserInterface
    {
        return new class($username, $role) implements UserInterface {
            public function __construct($username, $role)
            {
                $this->username = $username;
                $this->role = $role;
            }

            public function getUsername() : string
            {
                return $this->username;
            }

            public function getUserRole() : string
            {
                return $this->role;
            }
        };
    }
}
