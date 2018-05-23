<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

use Psr\Container\ContainerInterface;

/**
 * Produces a callable factory capable of itself producing a UserInterface
 * instance; this approach is used to allow substituting alternative user
 * implementations without requiring extensions to existing repositories.
 */
class DefaultUserFactory
{
    public function __invoke(ContainerInterface $container) : callable
    {
        return function (string $identity, array $roles = [], array $details = []) : UserInterface {
            return new DefaultUser($identity, $roles, $details);
        };
    }
}
