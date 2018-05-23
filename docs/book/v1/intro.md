# Zend Expressive Authentication

This component provides authentication abstraction using a middleware approach
for [PSR-7](http://www.php-fig.org/psr/psr-7/) and
[PSR-15](https://github.com/php-fig/fig-standards/tree/4b417c91b89fbedaf3283620ce432b6f51c80cc0/proposed/http-handlers)
applications.

Authentication is performed using the [AuthenticationMiddleware](https://github.com/zendframework/zend-expressive-authentication/blob/master/src/AuthenticationMiddleware.php)
class. This middleware consumes an [AuthenticationInterface](https://github.com/zendframework/zend-expressive-authentication/blob/master/src/AuthenticationInterface.php)
adapter to check if a [PSR-7](http://www.php-fig.org/psr/psr-7/) request is
authenticated or not. If authenticated, the middleware executes the next
middleware in the application, passing a [UserInterface](https://github.com/zendframework/zend-expressive-authentication/blob/master/src/UserInterface.php)
object via a request attribute. If the request is not authenticated, the
middleware returns a `401 Unauthorized` response based on the authentication
adapter provided.

The `Zend\Expressive\Authentication\UserInterface` is defined as follows:

```php
namespace Zend\Expressive\Authentication;

interface UserInterface
{
    /**
     * Get the unique user identity (id, username, email address, etc.).
     */
    public function getIdentity() : string;

    /**
     * Get all user roles.
     *
     * @return string[]
     */
    public function getRoles() : array;

    /**
     * Get the detail named $name if present; return $default otherwise.
     */
    public function getDetail(string $name, $default = null);

    /**
     * Get all additional user details, if any.
     */
    public function getDetails() : array;
}
```

The `UserInterface` attribute in the PSR-7 request can be used for checking
if a user has been authenticated or not, e.g. it can be used to verify the
authorization level of a user (for this scope, it is consumed by
[zend-expressive-authorization](https://github.com/zendframework/zend-expressive-authorization)).

## Default User class

We provide a default implementation of `UserInterface` via the class
`Zend\Expressive\Authentication\DefaultUser`. The class is final and immutable,
in order to prevent runtime changes.

Repositories will fetch user information based on the identity, including any
associated roles, and optionally any additional details (full name, email,
profile information, etc.). Often, user data and the objects representing them
are unique to the application. As such, the default repository implementations
we provide allow you to inject a _factory_ for producing the user. This factory
should be a PHP callable with the following signature:

```php
function (string $identity, array $roles = [], array $details = []) : UserInterface
```

In order to notify the package to use your custom factory, you will need to
create a service factory that returns it, and map it to the
`Zend\Expressive\Authentication\UserInterface` service.

We provide a service factory named `Zend\Expressive\Authentication\DefaultUserFactory`
that returns a user factory that produces a `DefaultUser` instance from the
arguments provided. This is mapped as follows in the service configuration:

```php
use Zend\Expressive\Authentication\DefaultUserFactory;
use Zend\Expressive\Authentication\UserInterface;

return [
    // ...
    'dependencies' => [
        'factories' => [
            // ...
            // Change the DefaultUserFactory::class with your custom service
            // factory that produces a user factory:
            UserInterface::class => DefaultUserFactory::class
        ]
    ]
];
```

## Usage in the route

The `AuthenticationMiddleware` can be used to authenticate a route. You just
need to add the class name of the middleware in the pipeline of a route.
As an example:

```php
$app->get('/admin/dashboard', [
    Zend\Expressive\Authentication\AuthenticationMiddleware::class,
    Admin\Action\Dashboard::class
], 'admin.dashboard');
```

In this example, the `AuthenticationMiddleware` is executed as first middleware
of the route `admin.dashboard`. If the user is authenticated, the application
executes the `Dashboard` action; otherwise it returns a `401 Unauthorized`
response.

## Choosing an authentication adapter

You can choose an authentication adapter and a user repository through the
service container configuration.

You need to specify the service for authentication using the name
`Zend\Expressive\Authentication\AuthenticationInterface` and the user registry
using the service name `Zend\Expressive\Authentication\UserRepositoryInterface::class`.

For instance, using `zend-servicemanager` you can easily configure these two
services using `aliases`. Below is an example of configuration using the *HTTP
Basic Access Authentication* adapter and the *htpasswd* file as the user
repository.

```php
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\Basic;
use Zend\Expressive\Authentication\UserRepository;
use Zend\Expressive\Authentication\UserRepositoryInterface;

return [
    // ...
    'dependencies' => [
        // ...
        'aliases' => [
            // ...
            AuthenticationInterface::class => Basic\BasicAccess::class,
            UserRepositoryInterface::class => UserRepository\Htpasswd::class
        ]
    ]
];

```
