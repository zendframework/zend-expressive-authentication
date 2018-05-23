<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
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
 *
 * It supports only bcrypt password hashing for security reasons.
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
     * @var callable
     */
    private $userFactory;

    public function __construct(
        PDO $pdo,
        array $config,
        callable $userFactory
    ) {
        $this->pdo = $pdo;
        $this->config = $config;

        // Provide type safety for the composed user factory.
        $this->userFactory = function (
            string $identity,
            array $roles = [],
            array $details = []
        ) use ($userFactory) : UserInterface {
            return $userFactory($identity, $roles, $details);
        };
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
            return ($this->userFactory)(
                $credential,
                $this->getUserRoles($credential),
                $this->getUserDetails($credential)
            );
        }
        return null;
    }

    /**
     * Get the user roles if present.
     *
     * @param string $identity
     * @return string[]
     */
    protected function getUserRoles(string $identity) : array
    {
        if (! isset($this->config['sql_get_roles'])) {
            return [];
        }

        if (false === strpos($this->config['sql_get_roles'], ':identity')) {
            throw new Exception\InvalidConfigException(
                'The sql_get_roles configuration setting must include an :identity parameter'
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

    /**
     * Get the user details if present.
     *
     * @param string $identity
     * @return string[]
     */
    protected function getUserDetails(string $identity) : array
    {
        if (! isset($this->config['sql_get_details'])) {
            return [];
        }

        if (false === strpos($this->config['sql_get_details'], ':identity')) {
            throw new Exception\InvalidConfigException(
                'The sql_get_details configuration setting must include a :identity parameter'
            );
        }

        try {
            $stmt = $this->pdo->prepare($this->config['sql_get_details']);
        } catch (PDOException $e) {
            throw new Exception\RuntimeException(sprintf(
                'Error preparing retrieval of user details: %s',
                $e->getMessage()
            ));
        }
        if (false === $stmt) {
            throw new Exception\RuntimeException(sprintf(
                'Error preparing retrieval of user details: unknown error'
            ));
        }
        $stmt->bindParam(':identity', $identity);

        if (! $stmt->execute()) {
            return [];
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
