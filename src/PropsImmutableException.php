<?php
/**
 * Created by PhpStorm.
 * User: jarda_p
 * Date: 9.9.2015
 * Time: 3:21
 */

namespace Ra;


class PropsImmutableException extends RaException
{

	/**
	 * PropsImmutableException constructor.
	 * @param string $string
	 */
	public function __construct($string)
	{
		parent::__construct('Props are immutable');
	}
}