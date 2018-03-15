<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication\UserRepository;

use Zend\Expressive\Authentication\Exception;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;

use function explode;
use function fclose;
use function fgets;
use function file_exists;
use function fopen;
use function password_verify;
use function sprintf;
use function strpos;
use function trim;

/**
 * Adapter for Apache htpasswd file
 * It supports only bcrypt hash password for security reason
 * @see https://httpd.apache.org/docs/2.4/programs/htpasswd.html
 */
class Htpasswd implements UserRepositoryInterface
{
    use UserTrait;

    /**
     * @var string
     */
    private $filename;

    /**
     * @throws Exception\InvalidConfigException
     */
    public function __construct(string $filename)
    {
        if (! file_exists($filename)) {
            throw new Exception\InvalidConfigException(sprintf(
                'I cannot access the htpasswd file %s',
                $filename
            ));
        }
        $this->filename = $filename;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(string $credential, string $password = null) : ?UserInterface
    {
        if (! $handle = fopen($this->filename, 'r')) {
            return null;
        }
        $found = false;
        while (! $found && ($line = fgets($handle)) !== false) {
            [$name, $hash] = explode(':', $line);
            if ($credential !== $name) {
                continue;
            }
            $hash = trim($hash);
            $this->checkBcryptHash($hash);
            $found = true;
        }
        fclose($handle);

        return $found
            && password_verify($password === null ? '' : $password, $hash)
                ? $this->generateUser($credential)
                : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getRolesFromUser(string $identity) : array
    {
        return [];
    }

    /**
     * Check bcrypt usage for security reason
     *
     * @throws Exception\RuntimeException
     */
    protected function checkBcryptHash(string $hash) : void
    {
        if (0 !== strpos($hash, '$2y$')) {
            throw new Exception\RuntimeException(
                'The htpasswd file uses not secure hash algorithm. Please use bcrypt.'
            );
        }
    }
}
