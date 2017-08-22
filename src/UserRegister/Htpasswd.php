<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */
namespace Zend\Expressive\Authentication\UserRegister;

use Zend\Expressive\Authentication\Exception;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRegisterInterface;

/**
 * Adapter for Apache htpasswd file
 * It supports only bcrypt hash password for security reason
 */

class Htpasswd implements UserRegisterInterface
{
    protected $authenticatedUser = null;

    public function __construct(string $filename)
    {
        if (! file_exists($filename)) {
            throw new Exception\InvalidConfigException(sprintf(
                "I cannot access the htpasswd file %s",
                $filename
            ));
        }
        $this->filename = $filename;
    }

    public function authenticate(string $username, string $password = null): ?UserInterface
    {
        if (! $handle = fopen($this->filename, "r")) {
            return null;
        }
        $found = false;
        while (! $found && ($line = fgets($handle)) !== false) {
            [$name, $hash] = explode(':', $line);
            if ($username !== $name) {
                continue;
            }
            $hash = trim($hash);
            $this->checkBcryptHash($hash);
            $found = true;
        }
        fclose($handle);

        return $found && password_verify($password, $hash) ?
               $this->generateUser($username, '') :
               null;
    }

    /**
     * Check bcrypt usage for security reason
     *
     * @param string $hash
     * @return void
     */
    protected function checkBcryptHash(string $hash): void
    {
        if ('$2y$' !== substr($hash, 0, 4)) {
            throw new Exception\RuntimeException(
                'The htpasswd file uses not secure hash algorithm. Please use bcrypt.'
            );
        }
    }

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
