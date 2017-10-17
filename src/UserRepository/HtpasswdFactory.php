<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\UserRepository;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\Exception;

class HtpasswdFactory
{
    public function __invoke(ContainerInterface $container) : Htpasswd
    {
        $htpasswd = $container->get('config')['user_repository']['htpasswd'] ?? null;
        if (null === $htpasswd) {
            throw new Exception\InvalidConfigException(
                'Htpasswd file name is not present in user_register config'
            );
        }
        return new Htpasswd($htpasswd);
    }
}
