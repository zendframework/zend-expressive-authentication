<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\Exception;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationMiddleware implements ServerMiddlewareInterface
{
    /**
     * @var AuthentiationInterface
     */
    protected $auth;

    /**
     * Constructor
     *
     * @param AuthenticationInterface $authentication
     * @return void
     */
    public function __construct(AuthenticationInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $user = $this->auth->authenticate($request);
        if (null !== $user) {
            return $delegate->process($request->withAttribute(UserInterface::class, $user));
        }
        return $this->auth->unauthorizedResponse($request);
    }
}
