<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthenticationInterface
     */
    protected $auth;

    public function __construct(AuthenticationInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $identity = $this->auth->authenticate($request);
        if (null !== $identity) {
            if ($identity instanceof UserInterface) {
                return $handler->handle($request->withAttribute(UserInterface::class, $identity));
            }
            return $handler->handle($request->withAttribute(IdentityInterface::class, $identity));
        }
        return $this->auth->unauthorizedResponse($request);
    }
}
