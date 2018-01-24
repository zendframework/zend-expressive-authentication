<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication\UserRepository;

use Zend\Expressive\Authentication\UserInterface;

trait UserTrait
{
    /**
     * Generate a user from identity and list of roles
     */
    protected function generateUser(string $identity, ?array $roles = null) : UserInterface
    {
        return new class($identity, $roles) implements UserInterface {
            private $identity;
            private $roles;

            public function __construct(string $identity, $roles)
            {
                $this->identity = $identity;
                $this->roles = $roles ?: [];
            }

            public function getIdentity() : string
            {
                return $this->identity;
            }

            public function getUserRoles() : array
            {
                return $this->roles;
            }
        };
    }
}
