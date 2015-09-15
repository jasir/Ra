<?php
/**
 * Created by PhpStorm.
 * User: jarda_p
 * Date: 9.9.2015
 * Time: 2:53
 */

namespace Ra;


class RaException extends \RuntimeException
{

	protected static function availablePropsMessage($props, $computed)
	{
		return
			'props: [' . implode(', ', array_keys($props)) . ']' .
			', computed: [' . implode(', ', array_keys($computed)) . ']';
	}


}