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
}
```

It contains only the `authenticate()` function, to authenticate the user's
credential. If authenticated, the result will be a `UserInterface` instance;
otherwise, a `null` value is returned.

## Configure the user repository

In order to use a user repository adapter, we need to configure it. For instance,
to consume an `htpasswd` file, we need to configure the path to the file.
Such configuration is provided in the `authentication` hierarchy provided to
your [PSR-11](http://www.php-fig.org/psr/psr-11/) container. We demonstrate
examples of such configuration below.

Using [Expressive](https://docs.zendframework.com/zend-expressive/), this
configuration can be stored in a file under the `/config/autoload/` folder.  We
suggest to use a `.local.php` suffix &mdash; e.g.
`/config/autoload/auth.local.php` &mdash; as local configuration is not stored
in the version control system.

You can also provide this configuration using a [ConfigProvider.php](https://github.com/zendframework/zend-expressive-authentication/blob/master/src/ConfigProvider.php)
class. [Read this blog post](https://framework.zend.com/blog/2017-04-20-config-aggregator.html)
for more information on config providers.

## htpasswd configuration

When using the htpasswd user repository implementation, you need only configure
the path to the `htpasswd` file:

```php
return [
    'authentication' => [
        'htpasswd' => 'insert the path to htpasswd file',
    ],
];
```

## PDO configuration

When using the PDO user repository adapter, you will need to provide PDO
connection parameters, as well as information on the table, field names, and a
SQL statement for retrieiving user roles:

```php
return [
    'authentication' => [
        'pdo' => [
            'dsn' => '',
            'username' => '',
            'password' => '',
            'table' => 'user table name',
            'field' => [
                'identity' => 'identity field name',
                'password' => 'password field name',
            ],
            'sql_get_roles'   => 'SQL to retrieve roles with :identity parameter',
            'sql_get_details' => 'SQL to retrieve user details by :identity',
        ],
    ],
];
```

The required parameters are `dsn`, `table`, and `field`.

The `dsn` value is the DSN connection string to be used to connect to the database.
For instance, using a SQLite database, a typical value is `sqlite:/path/to/file`.

The `username` and `password` parameters are optional parameters used to connect
to the database. Depending on the database, these parameters may not be required;
e.g. [SQLite](https://sqlite.org/) does not require them.

The `table` value is the name of the table containing the user credentials.

The `field` parameter contains the field name of the `identity` of the user and
the user `password.` The `identity` of the user can be a username, an email, etc.

The `sql_get_roles` setting is an optional parameter that contains the SQL query
for retrieving the user roles. The identity value must be specified using the
placeholder `:identity`. For instance, if a role is stored in a user table, a
typical query might look like the following:

```sql
SELECT role FROM user WHERE username = :identity
```

The `sql_get_details` parameter is similar to `sql_get_roles`: it specifies the
SQL query for retrieving the user's additional details, if any.

For instance, if a user has an email field this can be returned as additional
detail using the following query:

```sql
SELECT email FROM user WHERE username = :identity
```
