<?php
/**
 * Created by PhpStorm.
 * User: jarda_p
 * Date: 9.9.2015
 * Time: 2:58
 */

namespace Ra;


use Exception;

class PropExistsException extends RaException
{

	public function __construct($name, array $props, array $computed)
	{
		$message = "Property $name already exists! " .$this->availablePropsMessage($props, $computed);
		parent::__construct($message);
	}

}