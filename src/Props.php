<?php

namespace Ra;


class Props
{
	/** @var array */
	private $props = [];

	/** @var array  */
	private $computed = [];

	/** @var bool */
	private $immutable = false;


	/**
	 * Props constructor.
	 * @param array $props
	 */
	public function __construct(array $props = [])
	{
		$this->props = $props;
	}


	/**
	 * @param $name
	 * @return mixed
	 */
	final public function __get($name)
	{
		return $this->get($name);
	}


	/**
	 * @param $name
	 * @param $value
	 */
	final public function __set($name, $value)
	{
		$this->set($name, $value);
	}


	/**
	 * is triggered when invoking inaccessible methods in an object context.
	 *
	 * @param $name string
	 * @param $arguments array
	 * @return mixed
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 */
	final function __call($name, $arguments)
	{
		/** @var Closure $prop */
		$prop = $this->get($name);
		return call_user_func_array($prop, $arguments);
	}


	/**
	 * @param $name
	 * @return mixed
	 */
	final public function get($name)
	{
		if ($this->hasProp($name)) {
			return $this->props[$name];
		} elseif ($this->hasComputed($name)) {
			return call_user_func($this->computed[$name], $this);
		}
		throw new PropNotFoundException($name, $this->props, $this->computed);
	}


	/**
	 * @param $props
	 * @return $this
	 */
	final public function setProps($props)
	{
		foreach ($props as $key => $value) {
			$this->set($key, $value);
		}
		return $this;
	}


	/**
	 * @param $name
	 * @param $value
	 * @return $this
	 */
	final public function set($name, $value)
	{
		if ($this->immutable) {
			throw new PropsImmutableException('Props is in immutable mode.');
		}

		if ($this->hasAnyProp($name)) {
			throw new PropExistsException($name, $this->props, $this->computed);
		}
		$this->props[$name] = $value;
		return $this;
	}


	/**
	 * @param $name
	 * @return bool
	 */
	final public function hasAnyProp($name)
	{
		return $this->hasProp($name) || $this->hasComputed($name);
	}


	/**
	 * @return array
	 */
	final public function keys()
	{
		return array_keys($this->props + $this->computed);
	}


	/**
	 * @param $name
	 * @param $callable
	 */
	final public function computed($name, $callable)
	{
		if ($this->hasAnyProp($name)) {
			throw new PropExistsException($name, $this->props, $this->computed);
		}
		$this->computed[$name] = $callable;
	}


	/**
	 * @param array $clonedPropertiesNames
	 * @param array $newProperties
	 * @return Props
	 */
	final public function create($clonedPropertiesNames = [], $newProperties = [])
	{
		$props = (new self($newProperties));
		foreach ($clonedPropertiesNames as $propName) {
			if ($this->hasComputed($propName)) {
				if ($props->hasAnyProp($propName)) {
					throw new PropExistsException($propName, $props->props, $props->computed);
				}
				$props->computed($propName, $this->computed[$propName]);
			} else {
				$props->set($propName, $this->get($propName));
			}
		}
		return $props;
	}


	/**
	 * @param null $set
	 * @return bool
	 */
	final public function immutable($set = NULL)
	{
		if (is_bool($set)) {
			$this->immutable = $set;
		}
		return $this->immutable;
	}


	/**
	 * @param $name
	 * @return bool
	 */
	final protected function hasProp($name)
	{
		return array_key_exists($name, $this->props);
	}


	/**
	 * @param $name
	 * @return bool
	 */
	final protected function hasComputed($name)
	{
		return array_key_exists($name, $this->computed);
	}

}