<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-cache/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\Adapter;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;

class BasicAccess implements AuthenticationInterface
{
    /**
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $realm;

    /**
     * @var ResponseInterface
     */
    protected $responsePrototype;

    /**
     * Constructor
     *
     * @param UserRepositoryInterface $repository
     * @param string $realm
     * @param ResponseInterface $responsePrototype
     */
    public function __construct(
        UserRepositoryInterface $repository,
        string $realm,
        ResponseInterface $responsePrototype
    ) {
        $this->repository = $repository;
        $this->realm = $realm;
        $this->responsePrototype = $responsePrototype;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            return null;
        }
        if (! preg_match('/Basic ([a-zA-Z0-9\+\/\=]+)/', $authHeader[0], $match)) {
            return null;
        }
        [$username, $password] = explode(':', base64_decode($match[1]));

        return $this->repository->authenticate($username, $password);
    }

    /**
     * {@inheritDoc}
     */
    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responsePrototype->withHeader(
            'WWW-Authenticate',
            sprintf("Basic realm=\"%s\"", $this->realm)
        )->withStatus(401);
    }
}
