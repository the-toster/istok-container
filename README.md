# DI container

```shell
composer require istok/container
```

- contain handy `Container::call(\Closure $fn): mixed` method
- allow custom resolving by attributes (useful to fill DTO)
- allow param-name binding: `Container::bindArgument(string $name, string $for, \Closure $resolver)`
- implements PSR-11

## Registration
```php
// Registration of cachable entry
Container::singletone(string $id, string|\Closure $defitition);
// This entry will not be cached
Container::register(string $id, string|\Closure $defitition);

// parameter $name of $for::__construct() will be resolved by given closure 
Container::bindArgument(string $name, string $for, \Closure $resolver);
```
## Retrieving
```php
// take instance
Container::get(string $id);

/**
 * Call $fn with given arguments, using Container::get() for rest
 * @param array<string, mixed> $args
 */
Container::call(\Closure $fn, $args);

/**
 * Psalm-friendly version, contains actual type check, result should be typeof T
 * @template T
 * @param class-string<T> $id
 * @return T
 */
Container::construct(string $id): object;

```

## Resolution order
- check direct registration
- check attributes, use if any suited
- try to construct

## Closure arguments resolution
- apply explicitly provided arguments
- use `Container::get()` to resolve rest

## Resolving by attributes
If target class has attribute that implements `Istok\Container\Resolver` interface, instance of attribute will be constructed (by `Container::get`, not `ReflectionAttribute::newInstance()`), and then result of `Resolver::resolve($targetName, ...$attributeArgs)` will be returned as result.


## Service and Model resolving

According to [Object Design Style Guide](https://matthiasnoback.nl/book/style-guide-for-object-design/) by Matthias
Noback, there are [two types of objects](https://medium.com/swlh/objects-services-and-dependencies-58106df2ac2b):
> #### Two types of objects
> In an application there are typically two types of objects:
> 1. Service objects which either perform a task, or return a piece of information.
> 2. Objects that hold some data, and optionally expose some behavior for manipulating or retrieving that data.
>

First type have a well-known name, `Service`, and other one I called `Model` (I have to called it someway).  
While `Services` depends on both other `Services` and `Models`, `Models` depends only on other `Models` and `input`.
  
One of reasons why I want to create this container was the desire to be able to get well-typed DTOs filled from user input, or
config, as arguments.  
  
To achieve this, I added `Resolver` interface, which implementation can be used as attribute of `Model`.  
This allows to add contextual configuration for resolving this type of objects.

So, `Container` itself is mostly for resolving `Services`, and `ModelResolver` used for resolving `Models`, like request `DTOs`.
