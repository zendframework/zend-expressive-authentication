<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace Zend\Expressive\Authentication\UserRepository;

use PDO;
use Zend\Expressive\Authentication\Exception;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;

/**
 * Adapter for PDO database
 * It supports only bcrypt hash password for security reason
 */
class PdoDatabase implements UserRepositoryInterface
{
    use UserTrait;

    /**
     * Constructor
     */
    public function __construct(PDO $pdo, array $config)
    {
        $this->pdo = $pdo;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(string $credential, string $password = null) : ?UserInterface
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = :username',
            $this->config['table'],
            $this->config['field']['username']
        );
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $credential);
        if (! $stmt->execute()) {
            return null;
        }
        $result = $stmt->fetchObject();

        return password_verify($password, $result->{$this->config['field']['password']}) ?
               $this->generateUser($credential, $this->config['field']['role'] ?? '') :
               null;
    }
}
