# User Repository

An authentication adapter can pull user information from a variety
of repositories:

- an [htpasswd](https://httpd.apache.org/docs/current/programs/htpasswd.html) file
- a database
- a cache

zend-expressive-authentication provides an interface,
`Zend\Expressive\Authentication\UserRepositoryInterface`, to access this user
storage:

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
result will be a `UserInterface` instance, otherwise a null value is returned.

The second function is `getRolesFromUser()` and it specifies how to retrieve
the roles for a user. If a user does not have roles, this function will return
an empty array.


## Configure the user repository

In order to use a user repository adapter, we need to configure it. For instance,
to consume a `htpasswd` file, we need to specify the path of the file.
The configuration is provided in the `['config']['authentication']` value of
your [PSR-11](http://www.php-fig.org/psr/psr-11/) container.

Using [Expressive](https://docs.zendframework.com/zend-expressive/), this
configuration can be stored in a file under the `/config/autoload` folder.
We suggest to use a `.local.php` suffix, like `/config/autoload/auth.local.php`,
in order to use a local configuration that is not stored in the version control
system.

You can also provide this configuration using the [ConfigProvider.php](https://github.com/zendframework/zend-expressive-authentication/blob/master/src/ConfigProvider.php)
class. For more information, you can read this [blog post](https://framework.zend.com/blog/2017-04-20-config-aggregator.html).


## htpasswd configuration

The configuration of the `htpasswd` adapter requires only the file path, as
follows:

```php
'config' => [
    'authentication' => [
        'htpasswd' => 'insert the path to htpasswd file'
    ]
]
```

## PDO configuration

The configuration of the PDO adapter for the user repository, contains the
following parameters:

```php
'config' => [
    'authentication' => [
        'pdo' => [
            'dsn' => '',
            'username' => '',
            'password' => '',
            'table' => 'user table name',
            'field' => [
                'identity' => 'identity field name',
                'password' => 'password field name'
            ],
            'sql_get_roles' => 'SQL to retrieve roles with :identity parameter'
        ]
    ]
]
```

The required parameters are `dsn`, `table`, `field`.

The `dsn` value is the DSN connection string to be used to connect to the database.
For instance, using a SQLite database a typically value is `sqlite:/path/to/file`.

The `username` and `password` parameters are optional parameters used to connect
to the database. Depending on the database these parameters may not be required,
for instance using [SQLite](https://sqlite.org/).

The `table` value is the name of the table containing the user credentials.

The `field` parameter contains the field name of the `identity` of the user and
the user `password.` The `identity` of the user can be a username, an email, etc.

The `sql_get_roles` is an optional parameter that contains the SQL query for
retrieving the user roles. The identity value must be specified using the
placeholder `:identity`. For instance, if a role is stored in a user table a
typical query is as follows:

```sql
SELECT role FROM user WHERE username = :identity
```
