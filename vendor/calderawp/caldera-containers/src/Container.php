<?php

namespace calderawp\CalderaContainers;

use calderawp\CalderaContainers\Exceptions\NotFoundException;
use calderawp\CalderaContainers\Interfaces\Arrayable;
use Psr\Container\ContainerInterface;

/**
 * Class Container
 *
 * Container that is just pimple + interface implimentations
 *
 * @package calderawp\interop
 */
abstract class Container implements \JsonSerializable, Arrayable, ContainerInterface, \ArrayAccess
{

	/**
	 * @var \Pimple\Container
	 */
	private $pimple;

	/**
	 * @return \Pimple\Container
	 */
	protected function getPimple()
	{
		if (! $this->pimple) {
			$this->pimple = new \Pimple\Container();
		}
		return $this->pimple;
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}
	/**
	 * @inheritdoc
	 */
	public function toArray()
	{
		return (array)$this->getPimple();
	}

	/**
	 * @inheritdoc
	 */
	public function get($id)
	{
		if ($this->has($id)) {
			return $this->getPimple()->offsetGet($id);
		}
		throw new NotFoundException(sprintf('Service %s not found in container', $id));
	}

	/**
	 * @inheritdoc
	 */
	public function has($id)
	{
		return $this->getPimple()->offsetExists($id);
	}

	/**
	 * @inheritdoc
	 */
	public function set($id, $value)
	{
		$this->getPimple()->offsetSet($id, $value);
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset)
	{
		return $this->getPimple()->offsetExists($offset);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetGet($offset)
	{
		return $this->getPimple()->offsetGet($offset);
	}


	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value)
	{
		$this->getPimple()->offsetSet($offset, $value);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset)
	{
		$this->getPimple()->offsetUnset($offset);
	}
}
