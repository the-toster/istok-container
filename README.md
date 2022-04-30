# DI container
- implements PSR-11
- contain handy `Container::call(\Closure $fn): mixed` method
- allow param-name binding: `Container::bindArgument(string $name, string $for, \Closure $resolver)`
