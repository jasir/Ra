<?php
namespace Ra;

use Nette\Application\UI\Control;
use Nette\DI\PhpReflection;
use Nette\Utils\Validators;

/**
 * @author Jaroslav Povolný (jasir) <jaroslav.povolny@gmail.com>
 */
class BaseControl extends Control
{

	/** @var Props */
	protected $props;


	/**
	 * @param Props $props
	 */
	public function __construct(Props $props)
	{
		$this->setProps($props);
		parent::__construct();
	}


	/**
	 * @param Props $props
	 * @return $this
	 */
	public function setProps(Props $props)
	{
		$this->props = $props;
		$this->autowireProps();
		return $this;
	}


	/**
	 * @return Props
	 */
	public function getProps()
	{
		return $this->props;
	}


	protected function validateProps()
	{
		//todo:
	}


	/**
	 * @param  string      component name
	 * @return IComponent  the created component (optionally)
	 */
	protected function createComponent($name)
	{
		$component = parent::createComponent($name);
		if ($component) {
			return $component;
		}

		//get factory

		//call factory with injected dependencies

	}


	private function autowireProps()
	{
		$rc = $this->getReflection();

		foreach ($rc->getProperties() as $property) {

			$annotation = $property->getAnnotation('prop');
			if ($annotation) {

				$type = (string)$property->getAnnotation('var');
				$propName = isset($annotation['name']) ? $annotation['name'] : $property->name;

				//todo: implement @prop(type=specifictype)

				$value = $this->props->get($propName);

				if (in_array($type, ['array', 'int', 'numeric', 'string'])) {
					if (!Validators::is($value, $type)) {
						throw new PropValidationException(
							"Prop {$propName} is not of required type {$type}but " . $this->getObjectType($value)
						);
					}
				} else {
					$type = PhpReflection::expandClassName($type, $rc);
					if (!$value instanceOf $type) {
						throw new PropValidationException(
							"Prop {$propName} is not of required type {$type}, but " . $this->getObjectType($value)
						);
					}
				}

				$this->{$property->name} = $value;
			}

		}
	}


	private function getObjectType($value)
	{
		return is_object($value) ? get_class($value) : gettype($value);
	}


}