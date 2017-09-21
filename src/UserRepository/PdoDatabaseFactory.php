<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\UserRepository;

use PDO;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\Exception;

class PdoDatabaseFactory
{
    public function __invoke(ContainerInterface $container) : PdoDatabase
    {
        $pdo = $container->get('config')['user_register']['pdo'] ?? null;
        if (null === $pdo) {
            throw new Exception\InvalidConfigException(
                'PDO values are missing in user_register config'
            );
        }
        if (! isset($pdo['dsn'])) {
            throw new Exception\InvalidConfigException(
                'The PDO DSN value is missing in the configuration'
            );
        }
        if (! isset($pdo['table'])) {
            throw new Exception\InvalidConfigException(
                'The PDO table name is missing in the configuration'
            );
        }
        if (! isset($pdo['field']['username'])) {
            throw new Exception\InvalidConfigException(
                'The PDO username field is missing in the configuration'
            );
        }
        if (! isset($pdo['field']['password'])) {
            throw new Exception\InvalidConfigException(
                'The PDO password field is missing in the configuration'
            );
        }
        return new PdoDatabase(
            new PDO($pdo['dsn'], $pdo['username'] ?? null, $pdo['password'] ?? null),
            $pdo
        );
    }
}
