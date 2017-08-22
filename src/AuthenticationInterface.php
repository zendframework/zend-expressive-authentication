<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface AuthenticationInterface
{
    /**
     * Authenticate the PSR-7 request and return a valid user
     * or null if not authenticated
     *
     * @param ServerRequestInterface $request
     * @return UserInterface|null
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface;

    /**
     * Generate the unauthorized response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface;
}
