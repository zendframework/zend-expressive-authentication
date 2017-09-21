<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\Adapter;

use Psr\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Authentication\Exception;

class ZendAuthenticationFactory
{
    public function __invoke(ContainerInterface $container): ZendAuthentication
    {
        $auth = $container->has(AuthenticationService::class) ?
                $container->get(AuthenticationService::class) :
                null;
        if (null === $auth) {
            throw new Exception\InvalidConfigException(
                'The Zend\Authentication\AuthenticationService service is missing'
            );
        }
        $config = $container->get('config')['authentication'] ?? [];
        if (!isset($config['redirect'])) {
            throw new Exception\InvalidConfigException(
                'The redirect URL is missing for authentication'
            );
        }
        return new ZendAuthentication($auth, $config);
    }
}
