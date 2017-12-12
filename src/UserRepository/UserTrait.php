<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\UserRepository;

use Zend\Expressive\Authentication\UserInterface;

trait UserTrait
{
    /**
     * Generate a user from username and list of roles
     */
    protected function generateUser(string $username, ?array $roles = null) : UserInterface
    {
        return new class($username, $roles) implements UserInterface {
            private $username;
            private $roles;

            public function __construct(string $username, $roles)
            {
                $this->username = $username;
                $this->roles = $roles ?: [];
            }

            public function getUsername() : string
            {
                return $this->username;
            }

            public function getUserRoles() : array
            {
                return $this->roles;
            }
        };
    }
}
