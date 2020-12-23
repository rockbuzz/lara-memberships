# Lara Memberships

Account member management

<p><img src="https://github.com/rockbuzz/lara-memberships/workflows/Main/badge.svg"/></p>

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
$user->ownedAccounts(): HasMany;
$user->accounts(): BelongsToMany;
$user->allAccounts(): Collection;
$user->accountRole(Account $account): Role;
$user->hasAccountRole(Account $account, string $role): bool;
$user->accountPermissions($account): string[];
$user->hasAccountPermission(Account $account, string $permission): bool;
$user->ownsAccount(Account $account): bool;
```

```php
$account->owner(): User;
$account->members(): BelongsToMany;
$account->findMemberById(int $id): User;
$account->addMember(User $user, Role $role = null): self;
```

## Development environment

### Requirements
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)

### Usage
```bash
docker-compose up -d --build
```

```bash
docker-compose exec app composer ...
```
### Style Code

``` bash
composer cs
```
### Testing

``` bash
composer test
```

## License

The Lara Memberships is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).