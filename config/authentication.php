<?php
/**
 * Returns the configuration for the AuthenticationInterface and
 * UserRepositoryInterface services
 *
 * - Using BasicAccess for AuthenticationInterface:
 *
 * 'authentication' => [
 *     'realm' => 'insert the realm string'
 * ]
 *
 * - Using PhpSession for AuthenticationInterface
 *
 * Note: PhpSession uses 'username' and 'password' POST names as default
 * you can customize these field names using the following configuration:
 *
 * 'authentication' => [
 *     'username' => 'insert the custom field name for username',
 *     'password' => 'insert the custom field name for password',
 *     'redirect' => 'URL to redirect if no valid credentials'
 * ]
 *
 * - Using ZendAuthentication for AuthenticationInterface
 *
 * 'authentication' => [
 *     'redirect' => 'URL to redirect if no valid credentials',
 *     ''
 * ]
 * - Using Htpasswd for UserRepositoryInterface
 *
 * 'user_register' => [
 *     'htpasswd' => 'insert the path to htpasswd file'
 * ]
 *
 */
return [
];
