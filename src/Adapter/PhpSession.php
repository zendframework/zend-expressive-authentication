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

class PhpSession implements AuthenticationInterface
{
    protected $register;
    protected $config;
    protected $responsePrototype;

    public function __construct(
        UserRepositoryInterface $register,
        array $config,
        ResponseInterface $responsePrototype
    ) {
        $this->register = $register;
        $this->config = $config;
        $this->responsePrototype = $responsePrototype;
    }

    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        $cookies = $request->getCookieParams();
        if (isset($cookies[AuthenticationInterface::class])) {
            $this->setSessionId($cookies[AuthenticationInterface::class]);
            if (isset($_SESSION[UserInterface::class]) &&
                $_SESSION[UserInterface::class] instanceof UserInterface) {
                return $_SESSION[UserInterface::class];
            }
            return null;
        }
        if ('POST' === $request->getMethod()) {
            $params = $request->getParsedBody();
            $username = $this->config['username'] ?? 'username';
            $password = $this->config['password'] ?? 'password';
            if (!isset($params[$username]) || !isset($params[$password])) {
                return null;
            }
            $user = $this->register->authenticate(
                $params[$username],
                $params[$password]
            );
            if (null !== $user) {
                $this->setSessionId(bin2hex(random_bytes(20)));
                $_SESSION[UserInterface::class] = $user;
            }
            return $user;
        }
        return null;
    }

    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responsePrototype->withHeader(
            'Location',
            $this->config['redirect']
        )->withStatus(301);
    }

    private function setSessionId(string $id): void
    {
        session_name(AuthenticationInterface::class);
        session_id($id);
        session_start();
    }
}
