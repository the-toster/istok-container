# DI container

- implements PSR-11
- allow custom resolving by attributes (useful to fill DTO)
- contain handy `Container::call(\Closure $fn): mixed` method
- allow param-name binding: `Container::bindArgument(string $name, string $for, \Closure $resolver)`

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
  
To achieve this, I added `ModelResolver` interface, which implementation can be used as attribute of `Model`.  
This allows to add contextual configuration for resolving this type of objects.

So, `Container` itself is mostly for resolving `Services`, and `ModelResolver` used for resolving `Models`, like request `DTOs`.
