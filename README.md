<div align="center">
    <h1>Auction API</h1>
</div>

## Description

Creation Auction api for school

## Requirements

- [Git](https://www.git-scm.com/)
- [PHP](https://www.php.net/): ```v8.3.*```
- [Composer](https://getcomposer.org/) : ```v2.6.6```

## Installation

### Recover the project

Get the project from github using :

- https:

```sh
git clone https://github.com/Ri087/auction_api.git
```

or

- ssh:

```sh
git clone https://github.com/Ri087/auction_api.git
```

Then, enter in the project folder

### Install dependencies

```sh
composer install
```

### Copy env

```sh
cp .env.example .env
```

### Generate keypair JWT

```sh
php bin/console lexik:jwt:generate-keypair
```

### Create Database

```sh
php bin/console doctrine:database:create
```

```sh
php bin/console doctrine:migration:migrate
```

### Generate DataFixture

```sh
php bin/console lexik:jwt:generate-keypair
```

### Start Project

```sh
php bin/console server:start
```

```sh
Admin{
    "username" : "admin",
    "password" : "admin"
    }
User{
    "username" : "public",
    "password" : "public"
    }
```

owner :@CezGain @Ri087
