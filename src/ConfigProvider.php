<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
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
            'authentication' => $this->getAuthenticationConfig(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getAuthenticationConfig() : array
    {
        return [
            /* Values will depend on user repository and/or adapter.
             *
             * Example: using htpasswd UserRepositoryInterface implementation:
             *
             * 'user_register' => [
             *     'htpasswd' => 'insert the path to htpasswd file'
             * ]
             */
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'aliases' => [
                // Provide an alias for the AuthenticationInterface based on the adapter you are using.
                // AuthenticationInterface::class => Basic\BasicAccess::class,
                // Provide an alias for the UserRepository adapter based on your application needs.
                // UserRepositoryInterface::class => UserRepository\Htpasswd::class
            ],
            'factories' => [
                AuthenticationMiddleware::class => AuthenticationMiddlewareFactory::class,
                UserRepository\Htpasswd::class => UserRepository\HtpasswdFactory::class,
                UserRepository\PdoDatabase::class => UserRepository\PdoDatabaseFactory::class
            ]
        ];
    }
}
