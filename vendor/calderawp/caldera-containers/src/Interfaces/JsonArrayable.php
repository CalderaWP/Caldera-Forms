<?php


namespace calderawp\interop\Interfaces;

use calderawp\CalderaContainers\Interfaces\Arrayable;

/**
 * Interface JsonArrayable
 *
 * Interface that all objects that covnert to an array that is then used to convert to JSON MUST Impliment
 */
interface JsonArrayable extends Arrayable, \JsonSerializable
{

}
