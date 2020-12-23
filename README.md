# Lara Memberships

Account member management

## Requirements

PHP >=7.2

## Install

```bash
$ composer require rockbuzz/lara-memberships
```

```php
$ php artisan vendor:publish --provider="Rockbuzz\LaraMemberships\ServiceProvider"
```

```php
$ php artisan migrate
```

config/memberships.php
```php
...
'rbac' => [
    /* 'admin' => [
        '*'
    ],
    'editor' => [
        '*.create',
        '*.update',
        'posts.delete'
    ] */
]
```

Add the `HasMemberships` attribute to the user model

## Usage

```php
use Rockbuzz\LaraMemberships\Account;

class User
{
    use HasMemberships
}
```

```php
$user->ownedAccounts();
$user->accounts();
$user->allAccounts();
$user->accountRole(Account $account);
$user->hasAccountRole(Account $account, string $role);
$user->hasAccountPermission(Account $account, string $permission);
$user->accountPermissions($account);
$user->ownsAccount(Account $account);
```

## License

The Lara Memberships is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).