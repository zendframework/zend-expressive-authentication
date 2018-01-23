<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace Zend\Expressive\Authentication\UserRepository;

use PDO;
use PDOException;
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
            "SELECT %s FROM %s WHERE %s = :identity",
            $this->config['field']['password'],
            $this->config['table'],
            $this->config['field']['identity']
        );

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new Exception\RuntimeException(sprintf(
                "Error during the user authentication",
                $e->getMessage()
            ));
        }
        if (false === $stmt) {
            throw new Exception\RuntimeException(
                "Error during the user authentication, please check the configuration"
            );
        }
        $stmt->bindParam(':identity', $credential);
        $stmt->execute();

        $result = $stmt->fetchObject();
        if (! $result) {
            return null;
        }

        return password_verify($password, $result->{$this->config['field']['password']})
            ? $this->generateUser($credential, $this->getRolesFromUser($credential))
            : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getRolesFromUser(string $identity) : array
    {
        if (! isset($this->config['sql_get_roles'])) {
            return [];
        }

        if (false === strpos($this->config['sql_get_roles'], ':identity')) {
            throw new Exception\InvalidConfigException(
                'The sql_get_roles configuration setting must include a :identity parameter'
            );
        }

        try {
            $stmt = $this->pdo->prepare($this->config['sql_get_roles']);
        } catch (PDOException $e) {
            throw new Exception\RuntimeException(sprintf(
                "Error on sql_get_rols configuration: %s",
                $e->getMessage()
            ));
        }
        if (false === $stmt) {
            throw new Exception\RuntimeException(
                "Error on sql_get_rols configuration"
            );
        }
        $stmt->bindParam(':identity', $identity);

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
