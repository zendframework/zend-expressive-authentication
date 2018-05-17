<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

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
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var array
     */
    private $config;

    /**
     * @var UserInterface
     */
    private $user;

    public function __construct(PDO $pdo, array $config, UserInterface $user)
    {
        $this->pdo = $pdo;
        $this->config = $config;
        $this->user = $user;
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

        $stmt = $this->pdo->prepare($sql);
        if (false === $stmt) {
            throw new Exception\RuntimeException(
                'An error occurred when preparing to fetch user details from ' .
                'the repository; please verify your configuration'
            );
        }
        $stmt->bindParam(':identity', $credential);
        $stmt->execute();

        $result = $stmt->fetchObject();
        if (! $result) {
            return null;
        }

        if (password_verify($password, $result->{$this->config['field']['password']})) {
            $this->user->setIdentity($credential);
            $this->user->setRoles($this->getRolesFromUser($credential));
            return $this->user;
        }
        return null;
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
                'Error preparing retrieval of user roles: %s',
                $e->getMessage()
            ));
        }
        if (false === $stmt) {
            throw new Exception\RuntimeException(sprintf(
                'Error preparing retrieval of user roles: unknown error'
            ));
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
