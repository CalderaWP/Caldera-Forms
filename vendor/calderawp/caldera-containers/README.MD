# Caldera Containers
A collection of useful containers.

## Containers

* `calderawp\CalderaContainers\Container`
Basic PSR-11 compatible container decorating Pimple.
    - Is abstract.
    - Converts to arrays. `calderawp\CalderaContainers\Interfaces\Arrayable`
    - Converts to JSON. `JsonSerializable`

* `calderawp\CalderaContainers\ControlledContainer`
Extends the base container but only allows in specified attributes.
    - Is abstract
* `calderawp\CalderaContainers\Service\Coantainer`
A basic service container.

## Stuff.
Copyright 2018 CalderaWP LLC. License: GPL v2 or later.
