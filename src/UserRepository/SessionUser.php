<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication\UserRepository;

use Zend\Expressive\Authentication\UserInterface;

class SessionUser implements UserInterface
{
    /** @var string */
    private $identity;

    /** @var array */
    private $roles;

    public static function fromState(array $data) : UserInterface
    {
        $user = new self();

        $user->identity = (string) $data['identity'];
        $user->roles    = (array) $data['roles'];

        return $user;
    }

    public function toState() : array
    {
        return [
            'identity' => $this->identity,
            'roles'    => $this->roles,
        ];
    }

    public function getIdentity() : string
    {
        return $this->identity;
    }

    public function getUserRoles() : array
    {
        return $this->roles;
    }
}
