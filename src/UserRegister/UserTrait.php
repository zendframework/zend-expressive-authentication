<?php
namespace Zend\Expressive\Authentication\UserRegister;

use Zend\Expressive\Authentication\UserInterface;

trait UserTrait
{
    protected function generateUser(string $username, string $role): UserInterface
    {
        return new class($username, $role) implements UserInterface {

            public function __construct($username, $role)
            {
                $this->username = $username;
                $this->role = $role;
            }

            public function getUsername(): string
            {
                return $this->username;
            }
            
            public function getUserRole(): string
            {
                return $this->role;
            }
        };
    }
}
