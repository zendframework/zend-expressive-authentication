# User Repository

An authentication adapter can take the information about the users from
different repository: a [htpasswd](https://httpd.apache.org/docs/current/programs/htpasswd.html)
file, a database, a custom repository, etc. We provided an interface, the
`Zend\Expressive\Authentication\UserRepositoryInterface`, to access the user
storage. This interface is reported below:

```php
namespace Zend\Expressive\Authentication;

interface UserRepositoryInterface
{
    /**
     * Authenticate the credential (username) using a password
     * or using only the credential string (e.g. token based credential)
     * It returns the authenticated user or null.
     *
     * @param string $credential can be also a token
     */
    public function authenticate(string $credential, string $password = null) : ?UserInterface;

    /**
     * Get the user roles if present.
     *
     * @param string $username
     * @return string[]
     */
    public function getRolesFromUser(string $username) : array;
}
```
It contains two functions: `authenticate()` and `getRolesFromUser()`. The first
is used to authenticate using the user's credential. If authenticated, the
result will be a UserInterface instance, otherwise a null value is returned.

The second function is `getRolesFromUser()` and it specify how to retrieve
the roles of a user. If a user does not have roles, this function will return
an empty array.
