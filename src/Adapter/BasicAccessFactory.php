<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\Adapter;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\UserRegisterInterface;
use Zend\Expressive\Authentication\Exception;

class BasicAccessFactory
{
    public function __invoke(ContainerInterface $container) : BasicAccess
    {
        $userRegister = $container->has(UserRegisterInterface::class) ?
                        $container->get(UserRegisterInterface::class) :
                        null;
        if (null === $userRegister) {
            throw new Exception\InvalidConfigException(
                'UserRegisterInterface service is missing for authentication'
            );
        }
        $realm = $container->get('config')['authentication']['realm'] ?? null;
        if (null === $realm) {
            throw new Exception\InvalidConfigException(
                'Realm value is not present in authentication config'
            );
        }
        return new BasicAccess($userRegister, $realm);
    }
}
