```
$ docker build .
$ docker run -p 0.0.0.0:8080:80 6da5ae3dbc78
$ docker ps
$ docker stop 6da5ae3dbc78
```

1. [Language Features](#language-features)
  * [The new PHP](#the-new-php)
  * [Features](#features)
2. [Good Practices](#good-practices)
3. [Deployment, Testing, and Tuning](#deployment-testing-and-tuning)

# Language Features

## The new PHP

* it's a scripting language 
* more smaller specialized components.
* Composer - dependency manager.
* Provisioning tools: Ansible, Chef, Puppet.
* PSRs - community standards.
* PHPUnit - testing

PHP Engines:
* Zend Engine (original) - Rasmus Lerdorf, Andi Gutmans, and Zeev Suraski
* HipHop Virtual Machine (HHVM) from FB.
  * PHP and Hack interpreter that uses a just in time (JIT) compiler to improve performance and reduce memory usage.

Hack => new programming language built on top of PHP.

## Features

[GitHub Repo](https://github.com/codeguy/modern-php)

### Namespaces

* organize code into a virtual hierarchy.

```php 
namespace Symfony\Component\HttpFoundation;
```

* namespace declaration is always at the top.
* a namespace organizes related PHP classes like a filesystem.
* PHP namespaces are a virtual concept.
* with namespaces you can use third party code with no name collisions.

PHP let up import and alias namespaced code.

Ex. without alias:
```php
$response = new \Symfony\Component\HttpFoundation\Response('Ooops', 400);
$response->send();
```

Ex. with alias:
```php
use Symfony\Component\HttpFoundation\Response;

$response = new Response('Ooops', 400);
$response->send();
```

Ex. custom naming an alias (the default is the same as the imported class name):
```php
use Symfony\Component\HttpFoundation\Response as Res;

$response = new Res('Ooops', 400);
$response->send();
```

To import functions use func:
```php
use func Namespace\functionName;

functionName();
```

To import a constant, change use to use constant:
```php
use constant Namespace\CONST_NAME;

echo CONST_NAME;
```

Tips:
* multiple imports: keep each use statement on its own line.
* keep one class per file.
* for global componets inside a namespaced scope, prefix with `\` for globals. (or it will try to find it in the current namespace)

### Code to an interface

Interface => we don't care *how* the code implements the interface, but just makes sure it's implemented following a contract.

If I write code that expects an interface, my code immediately knows how to use any object that implements that interface.

# Good Practices

## Standards

## Components

## Good Practices

# Deployment, Testing, and Tuning
