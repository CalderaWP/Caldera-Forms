<?php


namespace calderawp\CalderaContainers\Exceptions;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class Exception
 *
 * Generic Exception - All exceptions from this library MUST be this or a subclass of this.
 */
class Exception extends \Exception implements ContainerExceptionInterface
{

}
