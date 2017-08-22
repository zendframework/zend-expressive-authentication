<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication;

class ConfigProvider
{
    /**
     * Return the configuration array.
     */
    public function __invoke() : array
    {
        return [
            'dependencies'  => $this->getDependencies(),
            'authentication' => include __DIR__ . '/../config/authentication.php'
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'factories' => [
                AuthorizationMiddleware::class => AuthorizationMiddlewareFactory::class,
                Adapter\BasicAccess::class => Adapter\BasicAccessFactory::class,
                UserRegister\Htpasswd::class => UserRegister\HtpasswdFactory::class
            ],
            'aliases' => [
                // Change the alias value for Authorization adapter and
                // UserRegister adapter
                AuthorizationInterface::class => Adapter\BasicAccess::class,
                UserRegisterInterface::class => UserRegister\Htpasswd::class
            ]
        ];
    }
}
