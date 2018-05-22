<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

use Psr\Container\ContainerInterface;

class DefaultUserFactory
{
    public function __invoke(ContainerInterface $container) : callable
    {
        return function (string $identity, array $roles = [], array $details = []) {
            return new DefaultUser($identity, $roles, $details);
        };
    }
}
