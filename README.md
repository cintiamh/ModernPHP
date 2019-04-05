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

```php
interface Documentable {
  public function getId();
  public function getContent();
}

class HtmlDocument implements Documentable {
  ...
  public function __construct() { ... }
  public function getId() { ... }
  public function getContent() { ... }
}
```

### Traits

Trait is a partial class implementation that can be mixed into one or more existing PHP classes.

* say what a class can do (like an interface)
* provide a modular implementation (like a class)

Traits enable modular implementations that can be injected into otherwise unrelated classes. => code reuse.

```php
trait MyTrait {
  // trait implementation (looks like a class with no constructor)
}

class MyClass {
  use MyTrait;

  // Class implementation
}
```

### Generators

Generators are simple iterators.

Generators compute and yield iteration values on-demand.

You can iterate in only one direction (forward) with generators.

```php
function simpleGenerator() {
  yield 'value1';
  yield 'value2';
  yield 'value3';
}

foreach (myGenerator() as $yieldedValue) {
  echo $yieldedValue . PHP_EOL;
}
```

CSV generator example:

```php
function getRows($file) {
  $handle = fopen($file, 'rb');
  if (!$handle) {
    throw new Exception();
  }
  while(!feof($handle)) {
    yield fgetcsv($handle);
  }
  fclose($handle);
}

foreach(getRows('data.csv') as $row) {
  print_r($row);
}
```

This example allocates memory for only one CSV row at a time instead of reading the entire 4 GB CSV file into memory.

Generators are most useful for iterating large or numerically sequenced data sets with only a tiny amount of system memory.

If you require more versatility, use a prebuilt Iterator: https://www.php.net/manual/en/spl.iterators.php

### Closures

Closure => a function that encapsulates its surroundings state at the time it is created. The encapsulated state exists inside the closure even when the closure lives after its original environment ceases to exist.

Anonymous function => a function without a name. Useful as function or method callbacks.

If you inspect a PHP closure or anonymous function, they are instances of the `Closure` class.

```php
$closure = function ($name) {
  return sprintf('Hello %s', $name);
};

echo $closure("Josh");
```

Closures can be passed into other PHP functions as arguments.

```php
$numbersPlusOne = array_map(function ($number) {
  return $number + 1;
}, [1,2,3]);
print_r($numbersPlusOne);
```

#### Attach State

You must manually attach state to a PHP closure with the closure object's `bindTo()` method or the `use` keyword.

```php
function enclosePerson($name) {
  return function ($doCommand) use ($name) {
    return sprintf('%s, %s', $name, $doCommand);
  };
}

// Enclose "Clay" string into closure
$clay = enclosePerson('Clay');

// Invoke closure with command 
echo $clay('get me sweet tea!');
```

PHP closures are objects. Each closure instance has its own internal state that is accessible with the `$this` keyword.

Attaching closure state with the `bindTo` method:
```php
class App {
  protected $routes = [];
  protected $responseStatus = '200 OK';
  protected $responseContentType = 'text/html';
  protected $responseBody = 'Hello World';
  
  public function addRoute($routePath, $routeCallback) {
    $this->routes[$routePath] = $routeCallback->bindTo($this, __CLASS__);
  }
  
  public function dispatch($currentPath) {
    foreach ($this->routes as $routePath => $callback) {
      if ($routePath === $currentPath) {
        $callback();
      }
    }
    
    header('HTTP/1.1 ' . $this->responseStatus);
    header('Content-type: ' . $this->responseContentType);
    header('Content-length: ' . mb_strlen($this->responseBody));
    echo $this->responseBody;
  }
}

$app = new App();
$app->addRoute('/users/josh', function() {
  $this->responseContentType = 'application/json;charset=utf8';
  $this->responseBody = '{"name": "Josh"}';
});
$app->dispatch('/users/josh');
```

### Zend OPcache

Bytecode cache.

Needs activation:
```
--enable-opcache
```

### Built-in HTTP server

Simple HTTP server for dev.

#### Start the server

Navigate to your project's root.
```
$ php -S localhost:4000
```

Let other machines in the network to access your dev:
```
$ php -S 0.0.0.0:4000
```

#### Configure the Server

```
$ php -S localhost:8000 -c app/config/php.ini
```

#### Router Scripts

The built-in server doesn't support .htaccess files.

The router script == hardcoded .htaccess file.

```
$ php -S localhost:8000 router.php
```

#### Detect the built-in server

```php
if (php_sapi_name() === 'cli-server') {
  // PHP web server
} else {
  // Other web server
}
```

#### Drawbacks

* handles one request at a time.
* Each HTTP request is blocking.
* supports a limited number of mimetypes.
* has limited URL rewriting with router scripts.

# Good Practices

## Standards

* Macro frameworks:
  * Symfony
  * Laravel
* Micro frameworks
  * Silex
  * Slim

### PHP-FIG to the Rescue

PHP Framework Interop Group => creates recommendations that PHP frameworks can implement to improve communication and sharing with other frameworks.

### Framework Interoperability

Goal => Interoperability => working together via interfaces, autoloading, and style.

* Interface: PHP frameworks work together via shared interfaces.
* Autoloading: a PHP class is automatically located and loaded on-demand by the PHP interpreter during runtime.
* Style: If all frameworks use the same style => less friction to get started.

### What is a PSR?

PHP standards recommendation.

PHP-FIG recommendations.

Each PHP-FIG recommendation solves a specific problem that is frequently encountered by most PHP frameworks.

### PSR-1: Basic Code Style

* PHP tags: use either `<?php ?>` or `<?= ?>` tags.
* Encoding: UTF-8.
* Objective: single file either define symbols or perform an action that has side effects, never both.
* Autoloading: support PSR-4.
* Class names: `CamelCase` / `TitleCase`
* Constant names: UPPERCASE characters with underscores.
* Method names: `camelCase`

### PSR-2: Strict Code Style

The PSR-2 requires PSR-1 code style.

* Indentation: Indent code with several space characters (4 spaces).
* Files and lines: 
  * end files with a single blank line, not include a trailing `?>` PHP tag.
  * each line not exceed 80 characters. (must not exceed 120 chars)
  * each line must not have trailing white space.

## Components

## Good Practices

# Deployment, Testing, and Tuning
