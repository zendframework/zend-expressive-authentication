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
     * @var PDO
     */
    private $pdo;

    /**
     * @var array
     */
    private $config;

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
            "SELECT %s FROM %s WHERE %s = :username",
            $this->config['field']['password'],
            $this->config['table'],
            $this->config['field']['username']
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $credential);

        if (! $stmt->execute()) {
            return null;
        }

        $result = $stmt->fetchObject();

        return password_verify($password, $result->{$this->config['field']['password']})
            ? $this->generateUser($credential, $this->getRolesFromUser($credential))
            : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getRolesFromUser(string $username) : array
    {
        if (! isset($this->config['sql_get_roles'])) {
            return [];
        }

        if (false === strpos($this->config['sql_get_roles'], ':username')) {
            throw new Exception\InvalidConfigException(
                'The sql_get_roles configuration setting must include a :username parameter'
            );
        }

        $stmt = $this->pdo->prepare($this->config['sql_get_roles']);
        $stmt->bindParam(':username', $username);

        if (! $stmt->execute()) {
            return [];
        }

        $roles = [];
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $role) {
            $roles[] = $role[0];
        }
        return $roles;
    }
}
