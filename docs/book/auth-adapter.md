# Authentication adapters

The authentication adapters for `zend-expressive-authentication` implement the
interface `AuthenticationInterface` reported below:

```php
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
```

This interface contains two functions: `authenticate()` to check if a PSR-7
request contains a valid credential and `unauthorizedResponse()` to return the
unauthorized response.

We provided 4 authentication adapters:

- [zend-expressive-authentication-basic](https://github.com/zendframework/zend-expressive-authentication-basic),
  for [Basic Access Authentication](https://en.wikipedia.org/wiki/Basic_access_authentication)
  supporting only `bcrypt` as password hashing algorithm (for security reason);
- [zend-expressive-authentication-session](https://github.com/zendframework/zend-expressive-authentication-session),
  for authenticate username and password credentials using PHP session;
- [zend-expressive-authentication-zendauthentication](https://github.com/zendframework/zend-expressive-authentication-zendauthentication),
  supporting the [zend-authentication](https://github.com/zendframework/zend-authentication)
  component;
- [zend-expressive-authentication-oauth2](https://github.com/zendframework/zend-expressive-authentication-oauth2),
  supporting [OAuth2](https://oauth.net/2/) authentication framework.
