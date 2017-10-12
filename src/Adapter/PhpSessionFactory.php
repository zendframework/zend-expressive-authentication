<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\Adapter;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\Exception;
use Zend\Expressive\Authentication\ResponsePrototypeTrait;
use Zend\Expressive\Authentication\UserRepositoryInterface;

class PhpSessionFactory
{
    use ResponsePrototypeTrait;

    public function __invoke(ContainerInterface $container): PhpSession
    {
        $userRegister = $container->has(UserRepositoryInterface::class) ?
                        $container->get(UserRepositoryInterface::class) :
                        null;
        if (null === $userRegister) {
            throw new Exception\InvalidConfigException(
                'UserRepositoryInterface service is missing for authentication'
            );
        }
        $config = $container->get('config')['authentication'] ?? [];
        if (!isset($config['redirect'])) {
            throw new Exception\InvalidConfigException(
                'The redirect configuration is missing for authentication'
            );
        }

        return new PhpSession(
            $userRegister,
            $config,
            $this->getResponsePrototype($container)
        );
    }
}
