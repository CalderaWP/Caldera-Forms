<?php


namespace calderawp\CalderaContainers;

/**
 * Class ControlledContainer
 *
 * Container that only allows whitelisted attributes
 *
 */
abstract class ControlledContainer extends Container
{

	/**
	 * @var  array
	 */
	protected $defaults;

	/**
	 * @var array
	 */
	protected $attributes;



	public function __construct(array $attributes = array(), array  $defaults = array())
	{
		$this->setProps($attributes, $defaults);
	}

	/**
	 * @inheritdoc
	 */
	public function get($id)
	{
		if ($this->allowed($id)) {
			if ($this->offsetExists($id)) {
				return $this->getPimple()->offsetGet($id);
			} elseif (array_key_exists($id, $this->defaults)) {
				return $this->defaults[ $id ];
			} else {
				return null;
			}
		}

		return null;
	}

	/**
	 * @param string $id
	 * @param mixed  $value
	 * @return $this
	 */
	public function set($id, $value)
	{
		if ($this->allowed($id)) {
			return parent::set($id, $value);
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function has($id)
	{
		return  $this->allowed($id)  && parent::has($id);
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function allowed($id)
	{
		return isset($id, $this->attributes);
	}



	/**
	 * @param array $attributes
	 * @param array $defaults
	 */
	private function setProps(array $attributes, array $defaults)
	{
		$this->attributes = $attributes;
		$this->defaults = $defaults;
		$this->propArrayMerge('attributes', $attributes);
		$this->propArrayMerge('defaults', $defaults);
	}

	/**
	 * @param $prop
	 * @param array $new
	 */
	private function propArrayMerge($prop, array  $new = array())
	{

		if (! empty($new)) {
			if (! empty($this->$prop)) {
				$this->$prop = $new;
			} else {
				$this->$prop = array_merge($new, $this->$prop);
			}
		}
	}
}
