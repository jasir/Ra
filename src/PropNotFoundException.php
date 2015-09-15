<?php
/**
 * Created by PhpStorm.
 * User: jarda_p
 * Date: 9.9.2015
 * Time: 2:38
 */

namespace Ra;


class PropNotFoundException extends RaException
{
	public function __construct($name, array $props, array $computed)
	{
		$message = "Prop '$name' not exits, available:  " .$this->availablePropsMessage($props, $computed);
		parent::__construct($message);
	}

}