<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface AuthenticationInterface
{
    /**
     * Authenticate the PSR-7 request and return a valid identity
     * or null if not authenticated
     */
    public function authenticate(ServerRequestInterface $request) : ?IdentityInterface;

    /**
     * Generate the unauthorized response
     */
    public function unauthorizedResponse(ServerRequestInterface $request) : ResponseInterface;
}
